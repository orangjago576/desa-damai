<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

trait ConvertsImagesToWebp
{
    /**
     * Konversi daftar field gambar ke WebP secara otomatis sebelum model disimpan.
     * 
     * @param array $fields Array berisi nama field gambar, misal: ['cover_image', 'og_image']
     * @return void
     */
    protected function convertToWebp(array $fields)
    {
        $manager = new ImageManager(new Driver());

        foreach ($fields as $field) {
            $path = $this->getAttribute($field);

            // Jika field tidak kosong dan bukan file webp
            if ($path && is_string($path) && !str_ends_with(strtolower($path), '.webp')) {
                // Pastikan file tersebut baru berubah (kotor)
                if ($this->isDirty($field)) {
                    $fullPath = Storage::disk('public')->path($path);
                    
                    if (file_exists($fullPath)) {
                        try {
                            // Baca dan konversi gambar (kualitas 80%)
                            $image = $manager->decodePath($fullPath);
                            
                            // Ganti ekstensi path menjadi webp
                            $pathInfo = pathinfo($path);
                            $newPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.webp';
                            $newFullPath = Storage::disk('public')->path($newPath);
                            
                            // Simpan versi webp (otomatis encode webp dari ekstensi)
                            $image->save($newFullPath, 80);
                            
                            // Hapus file asli
                            unlink($fullPath);
                            
                            // Update attribute ke path webp baru
                            $this->setAttribute($field, $newPath);
                        } catch (\Exception $e) {
                            // Abaikan error konversi jika gagal (biarkan format asli)
                        }
                    }
                }
            }
        }
    }
}
