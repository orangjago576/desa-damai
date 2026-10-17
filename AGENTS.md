# Konteks Proyek Website Resmi Desa (Program KKN)

File ini merupakan rujukan wajib di setiap sesi pengembangan. Wajib dibaca dan dipatuhi sebelum melakukan penambahan atau modifikasi kode apa pun.

---

## Konteks Proyek
- **Nama Proyek:** Website Desa [NAMA_DESA], Kecamatan [X], Kabupaten [Y]
- **Program:** Pembaruan dan pembenahan sistem informasi desa secara digital
- **Pengguna Akhir:** Warga desa (publik) dan perangkat desa (admin)
- **Karakteristik Admin:** Perangkat desa TIDAK memiliki latar belakang teknis
- **Karakteristik Pengelolaan:** Website akan diserahkan dan dikelola sendiri oleh pihak desa setelah program KKN selesai
- **Karakteristik Pengunjung:** Mayoritas pengunjung mengakses dari smartphone kelas bawah (low-end) dengan koneksi jaringan yang lambat/terbatas

---

## Stack Teknologi (Dikunci - Jangan Diganti)
- **Backend:** Laravel 11, PHP 8.2
- **Admin Panel:** Filament 3
- **Database:** MySQL 8
- **Frontend Publik:** Blade + Tailwind CSS
- **Interaksi Client-Side:** Alpine.js (interaksi ringan saja)
- **Peta:** Leaflet.js + OpenStreetMap (*BUKAN* Google Maps JS API — menghindari billing/ketergantungan berbayar pasca-KKN)
- **Target Deployment:** Shared Hosting cPanel

---

## Prinsip Teknis (Wajib Dipatuhi)
1. **Mobile-first:** Semua tata letak dan halaman publik wajib diuji dan tampil optimal pada lebar layar minimum 360px.
2. **Performa Ringan:** Halaman publik dilarang menggunakan framework JS berat atau animasi berlebih. Wajib seringan mungkin.
3. **Optimasi Aset:** Gambar wajib dikompresi otomatis/manual dan menerapkan lazy-loading (`loading="lazy"`).
4. **Kepatuhan Privasi (UU PDP):** TIDAK PERNAH menampilkan NIK, Nomor KK, atau data pribadi warga biasa di halaman publik. Ini adalah kepatuhan hukum, bukan preferensi desain.
5. **Privasi Kontak Pejabat:** Nomor HP pejabat desa (kepala desa, kepala dusun) BOLEH ditampilkan publik karena jabatan publik, TAPI WAJIB melalui kolom status persetujuan publikasi yang di-set manual oleh admin per kontak. Default nilai selalu: *tidak disetujui*.
6. **Bahasa Antarmuka:** Seluruh teks antarmuka (UI), pesan error, dan validasi menggunakan bahasa Indonesia yang baik dan lugas.
7. **Sederhana untuk Admin:** Admin panel Filament dibuat sesederhana mungkin, menggunakan label berbahasa Indonesia, dan menghindari istilah teknis yang membingungkan orang awam.
8. **Validasi & Error Handling:** Setiap fitur wajib memiliki validasi input yang ketat dan penanganan error yang jelas.
9. **Produksi Siap Pakai:** Dilarang meninggalkan TODO, placeholder palsu, atau kode setengah jadi.

---

## Ruang Lingkup Fitur (Final - Jangan Tambah di Luar Ini)
1. **Profil Kepala Desa:** Riwayat kepemimpinan dari masa ke masa, bukan hanya kades yang sedang aktif.
2. **Kegiatan Desa:** Pengganti "berita", dioptimalkan untuk kebutuhan crosspost ke media sosial (dilengkapi excerpt dan Open Graph meta image / `og_image`).
3. **Lembaga Desa:** BPD, PKK, Karang Taruna, LPM beserta struktur kepengurusan dan anggotanya.
4. **Peta Lokasi Penting:** Menampilkan kantor desa, rumah kepala desa, rumah kepala dusun, dan fasilitas umum penting lainnya dilengkapi koordinat serta tautan navigasi ke Google Maps.
5. **Direktori Kontak Penting:** Direktori nomor HP/WA pejabat desa, dengan syarat status persetujuan publikasi wajib aktif.

### Fitur Di Luar Scope (TIDAK Dikerjakan)
- APBDes / Transparansi Anggaran
- Layanan Administrasi Surat & Pengajuan Surat Online
- Statistik Kependudukan
- Direktori UMKM
- Galeri Foto Khusus
- Aspirasi Warga / Pengaduan Online

> **Catatan:** Jika sewaktu-waktu diminta menambahkan salah satu fitur di luar scope di atas, WAJIB konfirmasi ulang kepada pengguna terlebih dahulu sebelum mulai mengerjakan.

---

## Aturan Alur Kerja
- Kerjakan **hanya** tahap yang diminta secara spesifik oleh pengguna. Jangan mendahului ke tahap berikutnya.
- Setelah selesai setiap tahap, laporkan:
  1. File yang dibuat atau diubah
  2. Cara menguji hasilnya
  3. Hal-hal yang memerlukan verifikasi manual oleh pengguna
- Jika ada keputusan arsitektur atau desain yang ambigu, tanyakan terlebih dahulu sebelum menulis kode.
