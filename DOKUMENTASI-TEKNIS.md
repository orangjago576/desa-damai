# Dokumentasi Teknis Website Desa

Dokumen ini ditujukan untuk Pengembang (Developer), Tenaga IT Kecamatan, atau Mahasiswa KKN periode selanjutnya yang akan melakukan pemeliharaan, pengembangan, atau perpindahan server website desa ini.

## 1. Stack Teknologi
Aplikasi ini dikunci secara spesifik (oleh request desa/kecamatan) menggunakan stack berikut:
- **Framework Utama:** Laravel 11.x
- **Bahasa Pemrograman:** PHP 8.2 (Minimum)
- **Admin Panel:** Filament v3
- **Database:** MySQL 8.0 (atau SQLite untuk lokal)
- **Frontend Publik:** Blade Templating + Tailwind CSS
- **Interaksi Frontend:** Alpine.js (Ringan)
- **Peta Interaktif:** Leaflet.js + OpenStreetMap (Tanpa Google Maps API Key untuk menghindari biaya billing/kartu kredit bagi pihak desa).
- **Format Gambar:** Memakai library `intervention/image` (v3) dengan Trait khusus di backend yang secara otomatis membuang file JPG/PNG dan menyimpannya sebagai format `WebP` demi penghematan kuota shared hosting.

## 2. Struktur Folder & Kode Penting
- `app/Filament/Resources`: Seluruh konfigurasi UI/UX halaman Admin (CRUD).
- `app/Http/Controllers/PublicController.php`: Menangani seluruh akses data sisi pengunjung publik. Melakukan caching dengan `Cache::remember`.
- `app/Http/Controllers/PointOfInterestController.php`: Menangani logika peta dan memuat koordinat lokasi.
- `app/Models/ImportantContact.php`: Memiliki `scopePublished()` untuk memastikan kebocoran privasi tak terjadi.
- `app/Traits/ConvertsImagesToWebp.php`: *Trait* ajaib yang disuntikkan ke Model untuk konversi `.webp` otomatis saat *Event Model Saving* dipanggil.
- `resources/views/public`: Seluruh view blade untuk halaman publik.
- `resources/views/filament/forms/components/map-picker.blade.php`: Komponen khusus integrasi Peta Leaflet untuk di dalam Filament Admin.

## 3. Variabel Lingkungan (`.env`) Penting
Selain konfigurasi standar Laravel, perhatikan variabel custom berikut:
```env
# Identitas Desa (Tampil di Header & Footer)
VILLAGE_NAME="Desa Damai"
VILLAGE_DISTRICT="Kecamatan X"
VILLAGE_REGENCY="Kabupaten Y"

# Akun Superadmin Awal (Saat Menjalankan Seeder)
SUPERADMIN_NAME="Super Admin Desa"
SUPERADMIN_EMAIL="superadmin@desadamai.id"
SUPERADMIN_PASSWORD="password123"
```

## 4. Panduan Deploy ke cPanel (Shared Hosting)
Aplikasi ini dirancang untuk dapat berjalan mulus di *Shared Hosting* tradisional dengan terminal/SSH terbatas.

1. **Persiapan Lokal:**
   Jalankan `npm run build` dan `composer install --optimize-autoloader --no-dev`.
2. **Pengarsipan:**
   Kompres (ZIP) seluruh folder proyek (termasuk folder `vendor` dan `public/build`, **kecuali** `node_modules` dan `.git`).
3. **Unggah ke cPanel:**
   - Upload file ZIP ke folder domain (misal: `/home/user/public_html` atau `/home/user/desadamai`).
   - Ekstrak file ZIP.
4. **Penyesuaian Document Root:**
   Arahkan Document Root domain di pengaturan cPanel menuju ke dalam folder `/public` dari proyek Laravel ini.
5. **Database:**
   - Buat database MySQL dan user di cPanel.
   - Salin `.env.example` menjadi `.env`.
   - Edit `.env` dan masukkan kredensial DB. Ubah `APP_ENV=production` dan `APP_DEBUG=false`.
6. **Migrasi:**
   - Buka fitur **Terminal** di cPanel.
   - Arahkan ke folder proyek: `cd /home/user/public_html`.
   - Jalankan: `php artisan migrate --force`.
   - Jalankan (Opsional untuk data demo): `php artisan db:seed`.
7. **Symlink Storage:**
   Jalankan `php artisan storage:link`.

## 5. Cara Backup dan Restore Database
### Melakukan Backup (Otomatis & Manual)
Telah disediakan perintah artisan khusus:
```bash
php artisan db:backup
```
Perintah ini mengeksekusi `mysqldump` secara internal. Hasilnya berbentuk file `.sql` berekstensi timestamp yang disimpan di folder `storage/app/backups/`. 
*(Dapat dipanggil via Cron Job di cPanel untuk backup harian otomatis).*

### Melakukan Restore
1. Unduh file `.sql` dari folder `storage/app/backups/` melalui cPanel File Manager.
2. Buka **phpMyAdmin** di cPanel.
3. Pilih database desa, lalu klik tab **Import**.
4. Unggah file `.sql` tersebut dan klik Go.
(Bisa juga via SSH terminal: `mysql -u nama_user -p nama_database < file_backup.sql`).
