<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup database ke file .sql';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filename = 'backup-' . date('Y-m-d-H-i-s') . '.sql';
        $storagePath = storage_path('app/backups');

        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        $filePath = $storagePath . '/' . $filename;
        $connection = config('database.default');

        if ($connection === 'sqlite') {
            $dbPath = config('database.connections.sqlite.database');
            // Untuk sqlite, kita cukup copy file database-nya,
            // atau menggunakan sqlite3 dump jika tersedia di server
            // Demi kesederhanaan shared hosting cPanel yang pakai MySQL,
            // kita dukung dump sql
            $this->info("Menjalankan backup SQLite...");
            copy($dbPath, $storagePath . '/backup-' . date('Y-m-d-H-i-s') . '.sqlite');
            $this->info("Backup berhasil disimpan ke {$storagePath}");
            return Command::SUCCESS;
        }

        // MySQL dump untuk cPanel
        $dbHost = config('database.connections.mysql.host');
        $dbPort = config('database.connections.mysql.port');
        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');

        $command = "mysqldump --user={$dbUser} --password={$dbPass} --host={$dbHost} --port={$dbPort} {$dbName} > {$filePath}";

        $returnVar = null;
        $output = null;
        exec($command, $output, $returnVar);

        if ($returnVar === 0) {
            $this->info("Backup MySQL berhasil disimpan ke: {$filePath}");
            return Command::SUCCESS;
        } else {
            $this->error("Gagal melakukan backup. Pastikan mysqldump tersedia.");
            return Command::FAILURE;
        }
    }
}
