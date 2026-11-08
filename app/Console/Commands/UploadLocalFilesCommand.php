<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UploadLocalFilesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:upload-local-files 
                            {--disk= : Disk tujuan upload (default: config filesystems.default_upload_disk)} 
                            {--dry-run : Simulasi unggah tanpa menulis ke disk tujuan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Unggah berkas lokal dari storage/app/public ke disk penyimpanan cloud tujuan (S3 / Supabase Storage)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $targetDiskName = $this->option('disk') ?: config('filesystems.default_upload_disk', 's3');
        $isDryRun = (bool) $this->option('dry-run');

        $sourcePath = storage_path('app/public');

        $this->info("=== Upload Local Files Utility ===");
        $this->line("Direktori Sumber: <comment>{$sourcePath}</comment>");
        $this->line("Disk Tujuan: <comment>{$targetDiskName}</comment>");
        $this->line("Mode: " . ($isDryRun ? "<fg=yellow>DRY-RUN (Simulasi)</>" : "<fg=green>EKSEKUSI (Live)</>"));
        $this->newLine();

        if (!File::isDirectory($sourcePath)) {
            $this->warn("Direktori sumber '{$sourcePath}' tidak ditemukan atau kosong.");
            return self::SUCCESS;
        }

        $allFiles = File::allFiles($sourcePath);
        $totalFiles = count($allFiles);

        if ($totalFiles === 0) {
            $this->info("Tidak ada berkas yang ditemukan di '{$sourcePath}'.");
            return self::SUCCESS;
        }

        $this->line("Ditemukan: <info>{$totalFiles}</info> berkas lokal.");
        $this->newLine();

        $targetDisk = null;
        try {
            $targetDisk = Storage::disk($targetDiskName);
        } catch (\Throwable $e) {
            if (!$isDryRun) {
                $this->error("Gagal menginisialisasi disk '{$targetDiskName}': " . $e->getMessage());
                return self::FAILURE;
            }
            $this->warn("Disk '{$targetDiskName}' belum dikonfigurasi lengkap, menampilkan daftar semua berkas lokal:");
        }

        $uploaded = 0;
        $skipped = 0;
        $failed = 0;

        $normalizedSourcePath = rtrim(str_replace('\\', '/', realpath($sourcePath) ?: $sourcePath), '/');

        foreach ($allFiles as $file) {
            $fullPath = str_replace('\\', '/', $file->getRealPath());
            $relativePath = ltrim(substr($fullPath, strlen($normalizedSourcePath)), '/');

            // Abaikan file .gitignore atau file sistem tersembunyi
            if ($file->getFilename() === '.gitignore' || str_starts_with($file->getFilename(), '.')) {
                continue;
            }

            try {
                // Lewati jika berkas sudah ada di disk tujuan (jika target disk tersedia)
                if ($targetDisk) {
                    try {
                        if ($targetDisk->exists($relativePath)) {
                            $this->line("  [LEWATI] <comment>{$relativePath}</comment> (sudah ada di disk tujuan)");
                            $skipped++;
                            continue;
                        }
                    } catch (\Throwable $checkEx) {
                        if (!$isDryRun) {
                            throw $checkEx;
                        }
                    }
                }

                $fileSizeKb = number_format($file->getSize() / 1024, 2);

                if ($isDryRun) {
                    $this->info("  [DRY-RUN] Akan diunggah: <info>{$relativePath}</info> ({$fileSizeKb} KB)");
                    $uploaded++;
                    continue;
                }

                // Unggah berkas ke disk tujuan
                $stream = fopen($file->getRealPath(), 'r');
                $targetDisk->put($relativePath, $stream, 'public');
                if (is_resource($stream)) {
                    fclose($stream);
                }

                $this->info("  [SUKSES] Diunggah: <info>{$relativePath}</info> ({$fileSizeKb} KB)");
                $uploaded++;
            } catch (\Throwable $e) {
                $failed++;
                $errorMsg = $e->getMessage();
                $this->error("  [GAGAL] {$relativePath}: {$errorMsg}");
                Log::error("UploadLocalFilesCommand gagal pada {$relativePath}: {$errorMsg}", [
                    'exception' => $e,
                ]);
            }
        }

        $this->newLine();
        $this->info("=== Ringkasan Unggah Berkas ===");
        $this->table(
            ['Metrik', 'Nilai'],
            [
                ['Total Berkas Ditemukan', $totalFiles],
                ['Berkas ' . ($isDryRun ? 'Akan Diunggah' : 'Berhasil Diunggah'), $uploaded],
                ['Berkas Dilewati (Sudah Ada)', $skipped],
                ['Berkas Gagal', $failed],
            ]
        );

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
