# Deploy Laravel ke Vercel (Hobby)

Dokumen ini menjelaskan cara menjalankan aplikasi Laravel di Vercel menggunakan runtime komunitas `vercel-php`, sekaligus menjaga setup Render yang sudah ada.

## Ringkasan Perubahan untuk Vercel

- `api/index.php` — entrypoint serverless Vercel; menyiapkan direktori tulis di `/tmp` dan me-*require* `public/index.php`.
- `vercel.json` — konfigurasi build/runtime Vercel, routing aset Vite, dan env default untuk filesystem read-only.
- `.vercelignore` — mengecualikan file yang tidak perlu saat upload ke Vercel.
- `app/Providers/AppServiceProvider.php` — deteksi runtime Vercel dan mengalihkan `storage`, view compiled, cache file, dan session file ke `/tmp` agar Laravel tidak error `read-only file system`.
- `.env.example` — ditambah catatan konfigurasi Vercel (database eksternal, session/cookie, cache array, log stderr).
- `Dockerfile`, `nginx.conf`, `render-build.sh` — **tidak diubah**.

## Runtime PHP

`vercel.json` menggunakan `vercel-php@0.6.2` (PHP 8.2.x, Node autodetect) karena `composer.json` proyek mensyaratkan `php: ^8.2`. Referensi: [vercel-community/php](https://github.com/vercel-community/php). Jika runtime komunitas diperbarui, validasi kompatibilitas ekstensi (mis. `gd`, `pdo_mysql`/`pdo_pgsql`, `intl`) sebelum menaikkan versi.

## Environment Variables di Vercel Dashboard

Isi di **Vercel Project → Settings → Environment Variables**. Jangan commit nilai secret ke repo.

### Wajib

| Variabel | Contoh / Catatan |
|---|---|
| `APP_KEY` | Hasil `php artisan key:generate --show` (format `base64:...`). Wajib. |
| `APP_URL` | URL produksi Vercel, mis. `https://nama-proyek.vercel.app`. |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `DB_CONNECTION` | `mysql` atau `pgsql` (database eksternal). **Jangan** `sqlite`. |
| `DB_HOST` | Host database eksternal. |
| `DB_PORT` | `3306` (MySQL) atau `5432` (PostgreSQL). |
| `DB_DATABASE` | Nama database. |
| `DB_USERNAME` | Username database. |
| `DB_PASSWORD` | Password database. |

### Sudah di-set aman oleh `vercel.json` (boleh dioverride jika perlu)

| Variabel | Nilai default Vercel |
|---|---|
| `APP_CONFIG_CACHE` | `/tmp/bootstrap-cache/config.php` |
| `APP_EVENTS_CACHE` | `/tmp/bootstrap-cache/events.php` |
| `APP_PACKAGES_CACHE` | `/tmp/bootstrap-cache/packages.php` |
| `APP_ROUTES_CACHE` | `/tmp/bootstrap-cache/routes-v7.php` |
| `APP_SERVICES_CACHE` | `/tmp/bootstrap-cache/services.php` |
| `VIEW_COMPILED_PATH` | `/tmp/storage/framework/views` |
| `LOG_CHANNEL` | `stderr` |
| `SESSION_DRIVER` | `cookie` |
| `CACHE_STORE` / `CACHE_DRIVER` | `array` |
| `FILESYSTEM_DISK` | `local` |

### Opsional (sesuai kebutuhan proyek)

| Variabel | Catatan |
|---|---|
| `APP_NAME` | Nama aplikasi. |
| `APP_LOCALE` / `APP_FALLBACK_LOCALE` | `id` jika dipakai. |
| `APP_TIMEZONE` | `Asia/Jakarta` jika dipakai. |
| `SESSION_LIFETIME` | Umur sesi (menit). Sesi `cookie` tersimpan di browser; hindari data sesi besar. |
| `QUEUE_CONNECTION` | Gunakan `sync` untuk perilaku sederhana di serverless; hindari `database` queue worker di Vercel. |
| `MAIL_*` | Isi jika fitur email dipakai. |
| `VILLAGE_NAME`, `VILLAGE_DISTRICT`, `VILLAGE_REGENCY` | Identitas desa jika dipakai aplikasi. |
| `SUPERADMIN_*` | Hanya relevan saat seeding di lingkungan yang menjalankan Artisan; **jangan** jadikan mekanisme produksi tanpa proses aman. |

## Langkah Deploy

1. Pastikan repository terhubung ke Vercel (import project dari GitHub).
2. Di Vercel, biarkan deteksi framework mengikuti `vercel.json` proyek (framework di-set `null`; build memakai `installCommand`/`buildCommand` dari `vercel.json`).
3. Isi semua **Environment Variables wajib** di atas.
4. Deploy dari branch `main`. `installCommand` akan menjalankan `npm install` + `composer install`, lalu `buildCommand` menjalankan `npm run build` (Vite → `public/build`).
5. Setelah deploy sukses, buka URL produksi:
   - halaman utama harus merespons (bukan 404);
   - aset `/build/assets/*` harus termuat (CSS/JS Vite).
6. Jalankan migrasi database **di luar request Vercel** (dari mesin lokal/CI yang bisa mengakses DB eksternal):

   ```bash
   php artisan migrate --force
   ```

   Pastikan env lokal/CI menunjuk ke database eksternal yang sama.

## Batasan Serverless Vercel (Penting)

- **Filesystem read-only** kecuali `/tmp`. Data di `/tmp` bersifat sementara per instance dan **tidak persisten** antar request/deploy.
- **Tidak ada proses background persisten**. Queue worker, scheduler daemon, dan job panjang tidak cocok dijalankan di Vercel Hobby tanpa layanan terpisah.
- **Cold start** bisa terjadi; hindari operasi berat di bootstrap.
- **Database** harus eksternal (MySQL/PostgreSQL managed). Vercel tidak menyediakan database aplikasi dari konfigurasi ini.

## Pekerjaan Manual / Risiko yang Harus Ditangani

### 1. Upload File & Konversi Gambar (persistensi)

Kode saat ini menulis ke disk lokal (`public`/`local`) dan trait konversi WebP membaca/menulis path fisik lokal:

- `app/Traits/ConvertsImagesToWebp.php` — membaca file via `Storage::disk('public')->path(...)`, menyimpan WebP ke path lokal, lalu `unlink()` file asli. Di Vercel ini hanya berfungsi di `/tmp` (ephemeral) **jika** file ada di sana, tetapi hasil upload **tidak persisten**.
- `app/Filament/Resources/ActivityResource.php` — `FileUpload` `cover_image` (`activities/covers`) dan `og_image` (`activities/og`) tanpa disk eksplisit.
- `app/Filament/Resources/InstitutionMemberResource.php` — `photo_path` (`institution-members`).
- `app/Filament/Resources/InstitutionResource.php` — `logo_path` (`institutions`).
- `app/Filament/Resources/PointOfInterestResource.php` — `photo_path` (`poi`).
- `app/Filament/Resources/VillageHeadResource.php` — `photo_path` (`village-heads`).

**Saran (perlu konfirmasi sebelum ubah logika bisnis):** pindahkan upload ke object storage S3-compatible (AWS S3, Cloudflare R2, DigitalOcean Spaces, MinIO) atau Cloudinary melalui `config/filesystems.php` dan disk/driver terkait. Setelah itu:
- set `FILESYSTEM_DISK` (atau disk Filament) ke disk cloud;
- set env `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET`, `AWS_URL`, `AWS_ENDPOINT`, `AWS_USE_PATH_STYLE_ENDPOINT` sesuai provider;
- tinjau `ConvertsImagesToWebp` karena konversi berbasis path lokal (`path()`/`file_exists`/`unlink`) tidak langsung cocok untuk disk object storage; butuh strategi unduh-sementara/proses/upload-ulang atau pemrosesan di pipeline terpisah.

`php artisan storage:link` **tidak relevan** untuk deployment serverless Vercel.

### 2. Session & Cache

- `SESSION_DRIVER=cookie` dipakai untuk Vercel agar tidak bergantung pada tabel/file persisten.
- `CACHE_STORE=array` bersifat per-request/ephemeral; cocok untuk bootstrap aman, tetapi **tidak** untuk cache aplikasi yang harus bertahan. Jika butuh cache persisten, gunakan Redis/Memcached eksternal dan set `CACHE_STORE` sesuai.
- Jangan gunakan `database` session/cache/queue di Vercel kecuali Anda sengaja menanggung latency dan sudah menyiapkan tabel/migrasi yang sesuai.

### 3. Database Eksternal

- `config/database.php` sudah mendukung `mysql`, `pgsql`, dll via env; default lokal tetap `sqlite` di config, tetapi **di Vercel wajib** set `DB_CONNECTION=mysql` atau `pgsql` dan kredensial eksternal.
- Pastikan ekstensi PDO yang dibutuhkan tersedia di runtime `vercel-php` (PDO MySQL/PostgreSQL didukung oleh runtime komunitas; tetap verifikasi saat deploy).
- Untuk MySQL managed dengan TLS, set `MYSQL_ATTR_SSL_CA` jika provider mewajibkannya.

### 4. Verifikasi Pasca-Deploy

- Cek **Vercel Function Logs** untuk error `read-only file system`, permission `/tmp`, missing `APP_KEY`, atau koneksi DB.
- Pastikan route Filament/admin dan halaman publik yang bergantung pada data DB berfungsi setelah migrasi.
- Uji upload hanya setelah strategi storage persisten diputuskan; perilaku `/tmp` bukan solusi produksi.

## Menjaga Kompatibilitas Render

- Semua perubahan Vercel bersifat tambahan/terisolasi (`api/index.php`, `vercel.json`, `.vercelignore`, `VERCEL-DEPLOY.md`, deteksi Vercel di `AppServiceProvider`).
- `Dockerfile`, `nginx.conf`, dan `render-build.sh` tidak diubah.
- Deteksi Vercel hanya aktif saat env `VERCEL` ada, sehingga perilaku Render/hosting lain tidak terpengaruh.
