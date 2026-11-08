<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CopyDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:copy-data 
                            {--from=sqlite : Koneksi database sumber (default: sqlite)} 
                            {--to=pgsql : Koneksi database tujuan (default: pgsql)} 
                            {--dry-run : Simulasi tanpa menyalin data} 
                            {--tables= : Daftar tabel dipisah koma (opsional)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Salin data tabel konten dari koneksi sumber ke database tujuan (idempotent, boolean cast, sequence reset)';

    /**
     * Daftar tabel konten yang diizinkan untuk disalin,
     * diurutkan berdasarkan dependensi foreign key.
     */
    protected array $defaultTables = [
        'village_profiles',
        'activity_categories',
        'village_heads',
        'settings',
        'institutions',
        'institution_members',
        'activities',
        'points_of_interest',
        'important_contacts',
    ];

    /**
     * Tabel yang dilarang disalin.
     */
    protected array $blacklistedTables = [
        'users',
        'sessions',
        'cache',
        'cache_locks',
        'jobs',
        'job_batches',
        'failed_jobs',
        'migrations',
        'password_reset_tokens',
    ];

    /**
     * Kolom bertipe boolean per tabel untuk memastikan dikirim sebagai boolean asli ke Postgres.
     */
    protected array $booleanColumns = [
        'village_heads' => ['is_active'],
        'important_contacts' => ['is_approved_for_publication'],
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $fromConnection = $this->option('from') ?: 'sqlite';
        $toConnection = $this->option('to') ?: 'pgsql';
        $isDryRun = (bool) $this->option('dry-run');

        $tablesOption = $this->option('tables');
        if ($tablesOption) {
            $requestedTables = array_map('trim', explode(',', $tablesOption));
            $tables = array_values(array_diff($requestedTables, $this->blacklistedTables));
        } else {
            $tables = $this->defaultTables;
        }

        $this->info("=== Copy Data Utility ===");
        $this->line("Sumber: <comment>{$fromConnection}</comment>");
        $this->line("Tujuan: <comment>{$toConnection}</comment>");
        $this->line("Mode: " . ($isDryRun ? "<fg=yellow>DRY-RUN (Simulasi)</>" : "<fg=green>EKSEKUSI (Live)</>"));
        $this->line("Daftar Tabel: " . implode(', ', $tables));
        $this->newLine();

        // Jika koneksi sumber adalah sqlite, pastikan mengarah ke file database.sqlite lokal
        if ($fromConnection === 'sqlite') {
            $configuredPath = config('database.connections.sqlite.database');
            if (!file_exists($configuredPath) && file_exists(database_path('database.sqlite'))) {
                config(['database.connections.sqlite.database' => database_path('database.sqlite')]);
                DB::purge('sqlite');
            }
        }

        // Verifikasi koneksi sumber
        try {
            DB::connection($fromConnection)->getPdo();
        } catch (\Throwable $e) {
            $this->error("Gagal terhubung ke koneksi sumber '{$fromConnection}': " . $e->getMessage());
            return self::FAILURE;
        }

        // Verifikasi koneksi tujuan jika bukan dry-run
        if (!$isDryRun) {
            try {
                DB::connection($toConnection)->getPdo();
            } catch (\Throwable $e) {
                $this->error("Gagal terhubung ke koneksi tujuan '{$toConnection}': " . $e->getMessage());
                return self::FAILURE;
            }
        }

        $summary = [];

        foreach ($tables as $table) {
            $this->line("<comment>Tabel: {$table}</comment>");

            try {
                // Ambil semua baris dari database sumber (termasuk yang soft-deleted)
                $rows = DB::connection($fromConnection)->table($table)->get();
                $count = $rows->count();

                if ($count === 0) {
                    $this->info("  [INFO] Tabel kosong di database sumber (0 baris).");
                    $summary[$table] = ['status' => 'OK (Kosong)', 'rows' => 0];
                    continue;
                }

                $this->line("  Ditemukan: <info>{$count}</info> baris di sumber.");

                if ($isDryRun) {
                    $this->info("  [DRY-RUN] {$count} baris akan disalin ke {$toConnection}.");
                    $summary[$table] = ['status' => 'DRY-RUN', 'rows' => $count];
                    continue;
                }

                // Transformasi data untuk Postgres
                $preparedRows = [];
                $tableBooleans = $this->booleanColumns[$table] ?? [];

                // Cek ketersediaan user_id jika tabel activities
                $fallbackUserId = null;
                if ($table === 'activities') {
                    $fallbackUserId = DB::connection($toConnection)->table('users')->value('id');
                }

                foreach ($rows as $row) {
                    $record = (array) $row;

                    // Cast boolean columns ke true/false asli
                    foreach ($tableBooleans as $boolCol) {
                        if (array_key_exists($boolCol, $record) && !is_null($record[$boolCol])) {
                            $record[$boolCol] = (bool) $record[$boolCol];
                        }
                    }

                    // Khusus activities: pastikan user_id valid jika user lokal belum ada di tujuan
                    if ($table === 'activities' && !empty($record['user_id'])) {
                        $userExists = DB::connection($toConnection)->table('users')->where('id', $record['user_id'])->exists();
                        if (!$userExists && $fallbackUserId) {
                            $record['user_id'] = $fallbackUserId;
                        }
                    }

                    $preparedRows[] = $record;
                }

                // Chunk upsert untuk keandalan dan efisiensi
                $chunkSize = 100;
                $chunks = array_chunk($preparedRows, $chunkSize);
                $upsertedCount = 0;

                foreach ($chunks as $chunk) {
                    if (empty($chunk)) {
                        continue;
                    }

                    // Tentukan semua kolom yang akan diupdate saat konflik primary key 'id'
                    $firstRow = $chunk[0];
                    $columns = array_keys($firstRow);
                    $updateColumns = array_values(array_diff($columns, ['id', 'created_at']));

                    DB::connection($toConnection)->table($table)->upsert(
                        $chunk,
                        ['id'],
                        $updateColumns
                    );

                    $upsertedCount += count($chunk);
                }

                // Reset sequence primary key untuk Postgres
                $driverName = DB::connection($toConnection)->getDriverName();
                if ($driverName === 'pgsql') {
                    try {
                        DB::connection($toConnection)->statement(
                            "SELECT setval(pg_get_serial_sequence('\"{$table}\"', 'id'), COALESCE((SELECT MAX(id) FROM \"{$table}\"), 1), (SELECT MAX(id) FROM \"{$table}\") IS NOT NULL)"
                        );
                        $this->line("  Sequence '{$table}_id_seq' berhasil di-reset.");
                    } catch (\Throwable $seqErr) {
                        $this->warn("  Gagal reset sequence untuk {$table}: " . $seqErr->getMessage());
                    }
                }

                $this->info("  [SUKSES] {$upsertedCount} baris berhasil disalin/diperbarui.");
                $summary[$table] = ['status' => 'SUKSES', 'rows' => $upsertedCount];

            } catch (\Throwable $e) {
                $errorMsg = $e->getMessage();
                $this->error("  [ERROR] Gagal menyalin tabel {$table}: {$errorMsg}");
                Log::error("CopyDataCommand error pada tabel {$table}: {$errorMsg}", [
                    'exception' => $e,
                ]);
                $summary[$table] = ['status' => 'GAGAL: ' . $errorMsg, 'rows' => 0];
            }

            $this->newLine();
        }

        $this->newLine();
        $this->info("=== Ringkasan Salin Data ===");
        $tableData = [];
        foreach ($summary as $tbl => $info) {
            $tableData[] = [$tbl, $info['rows'], $info['status']];
        }
        $this->table(['Tabel', 'Jumlah Baris', 'Status'], $tableData);

        return self::SUCCESS;
    }
}
