<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

trait ConvertsImagesToWebp
{
    /**
     * Konversi daftar field gambar ke WebP secara otomatis sebelum model disimpan.
     * Bekerja baik di disk lokal maupun disk cloud (S3 / Supabase Storage).
     *
     * @param array $fields Array berisi nama field gambar, misal: ['cover_image', 'og_image']
     * @return void
     */
    protected function convertToWebp(array $fields): void
    {
        // Vercel serverless functions tidak menyertakan ekstensi GD / Imagick.
        // Lewati konversi jika tidak ada driver gambar yang tersedia.
        if (!extension_loaded('gd') && !extension_loaded('imagick')) {
            return;
        }

        $diskName = config('filesystems.default_upload_disk', 'public');
        $disk = Storage::disk($diskName);

        try {
            $manager = new ImageManager(new Driver());
        } catch (\Throwable $e) {
            Log::warning("Gagal inisialisasi ImageManager: " . $e->getMessage());
            return;
        }

        foreach ($fields as $field) {
            $path = $this->getAttribute($field);

            // Jika field tidak kosong dan bukan file webp
            if ($path && is_string($path) && !str_ends_with(strtolower($path), '.webp')) {
                // Pastikan file tersebut baru berubah (kotor)
                if ($this->isDirty($field)) {
                    if ($disk->exists($path)) {
                        $tempInput = tempnam(sys_get_temp_dir(), 'img_orig_');
                        $tempOutput = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'img_webp_' . uniqid('', true) . '.webp';

                        try {
                            // Unduh isi file dari disk ke path sementara lokal
                            file_put_contents($tempInput, $disk->get($path));

                            // Baca dan konversi gambar (kualitas 80%)
                            $image = $manager->decodePath($tempInput);

                            // Ganti ekstensi path menjadi webp
                            $pathInfo = pathinfo($path);
                            $dir = ($pathInfo['dirname'] === '.' || $pathInfo['dirname'] === '') ? '' : $pathInfo['dirname'] . '/';
                            $newPath = $dir . $pathInfo['filename'] . '.webp';

                            // Simpan versi webp ke file sementara
                            $image->save($tempOutput, 80);

                            // Upload file webp ke disk penyimpanan tujuan
                            $disk->put($newPath, file_get_contents($tempOutput), 'public');

                            // Hapus file asli dari disk
                            $disk->delete($path);

                            // Update attribute model ke path webp yang baru
                            $this->setAttribute($field, $newPath);
                        } catch (\Throwable $e) {
                            Log::warning("Gagal konversi gambar {$path} ke WebP pada model " . get_class($this) . ": " . $e->getMessage());
                            // Fallback: biarkan file asli tetap digunakan tanpa mengubah attribute
                        } finally {
                            // Bersihkan file sementara
                            if (file_exists($tempInput)) {
                                @unlink($tempInput);
                            }
                            if (file_exists($tempOutput)) {
                                @unlink($tempOutput);
                            }
                        }
                    }
                }
            }
        }
    }
}
