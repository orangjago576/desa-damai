<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\ActivityCategory;
use App\Models\ImportantContact;
use App\Models\Institution;
use App\Models\InstitutionMember;
use App\Models\PointOfInterest;
use App\Models\User;
use App\Models\VillageHead;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat User Admin Demo
        $admin = User::firstOrCreate(
            ['email' => 'admin@desadamai.id'],
            [
                'name' => 'Admin Desa Demo',
                'password' => bcrypt('password123'),
                'role' => 'admin',
            ]
        );

        // 2. Kades Demo
        VillageHead::firstOrCreate(
            ['name' => 'Bapak Budi Santoso'],
            [
                'welcome_speech' => 'Selamat datang di website resmi desa kita. Mari wujudkan desa digital yang transparan dan mandiri bersama-sama.',
                'period_start' => Carbon::parse('2022-01-01'),
                'period_end' => Carbon::parse('2028-01-01'),
                'is_active' => true,
                'order' => 1,
            ]
        );

        // 3. Kategori Kegiatan
        $catPembangunan = ActivityCategory::firstOrCreate(['name' => 'Pembangunan'], ['slug' => 'pembangunan']);
        $catSosial = ActivityCategory::firstOrCreate(['name' => 'Sosial & Kesehatan'], ['slug' => 'sosial-kesehatan']);
        $catBudaya = ActivityCategory::firstOrCreate(['name' => 'Budaya & Olahraga'], ['slug' => 'budaya-olahraga']);
        $catPendidikan = ActivityCategory::firstOrCreate(['name' => 'Pendidikan'], ['slug' => 'pendidikan']);

        // 4. Kegiatan Desa (dengan gambar sampul)
        $activities = [
            [
                'title' => 'Gotong Royong Pembersihan Irigasi',
                'slug' => 'gotong-royong-pembersihan-irigasi',
                'category_id' => $catSosial->id,
                'user_id' => $admin->id,
                'content' => '<p>Kegiatan gotong royong rutin membersihkan saluran air (irigasi) di Dusun 1 untuk persiapan musim penghujan. Warga berkumpul sejak pagi dengan alat-alat kerja seperti cangkul, bajak, dan karung. Semangat gotong royong terasa sangat tinggi di antara warga.</p>',
                'excerpt' => 'Warga Dusun 1 bersama perangkat desa melaksanakan kerja bakti pembersihan saluran irigasi.',
                'cover_image' => 'activities/covers/01M4HX569JBN5Q7VAK64YMW6T6.png',
                'activity_date' => Carbon::now()->subDays(2),
                'location' => 'Dusun 1, Desa Damai',
                'status' => 'published',
            ],
            [
                'title' => 'Musrenbangdes Tahun 2026',
                'slug' => 'musrenbangdes-tahun-2026',
                'category_id' => $catPembangunan->id,
                'user_id' => $admin->id,
                'content' => '<p>Musyawarah Perencanaan Pembangunan Desa (Musrenbangdes) dihadiri oleh BPD, perangkat desa, dan perwakilan warga dari seluruh dusun. Acara berlangsung di Balai Desa dan menghasilkan prioritas pembangunan untuk tahun anggaran mendatang.</p>',
                'excerpt' => 'Penetapan skala prioritas pembangunan tahun 2027 melalui Musrenbangdes.',
                'cover_image' => 'activities/covers/01M4HX853GM39R1NAW6XBY8XJ1.png',
                'activity_date' => Carbon::now()->subDays(5),
                'location' => 'Balai Desa Damai',
                'status' => 'published',
            ],
            [
                'title' => 'Senam Bersama di Lapangan Desa Damai',
                'slug' => 'senam-bersama-di-lapangan-desa-damai',
                'category_id' => $catBudaya->id,
                'user_id' => $admin->id,
                'content' => '<p>Kegiatan senam pagi rutin setiap hari Minggu di Lapangan Desa Damai dihadiri oleh ratusan warga dari berbagai usia. Dikoordinir oleh Karang Taruna dan didampingi instruktur senam profesional. Kegiatan ini bertujuan meningkatkan kesehatan dan kebersamaan warga.</p>',
                'excerpt' => 'Senam pagi rutin warga Desa Damai setiap hari Minggu pukul 06.00 WIB di Lapangan Desa.',
                'cover_image' => 'activities/covers/01M4HXA14K861V031SJG4TT8RT.png',
                'activity_date' => Carbon::now()->subDays(1),
                'location' => 'Lapangan Desa Damai',
                'status' => 'published',
            ],
            [
                'title' => 'Pelatihan Kewirausahaan UMKM Desa',
                'slug' => 'pelatihan-kewirausahaan-umkm-desa',
                'category_id' => $catPembangunan->id,
                'user_id' => $admin->id,
                'content' => '<p>Pelatihan kewirausahaan bagi pelaku UMKM desa bekerjasama dengan Dinas Koperasi dan UKM Kabupaten. Materi meliputi digital marketing, pengelolaan keuangan sederhana, dan standarisasi produk. Diikuti 30 pelaku UMKM dari berbagai dusun.</p>',
                'excerpt' => 'Pelatihan kapasitas UMKM desa untuk meningkatkan daya saing produk lokal.',
                'cover_image' => 'activities/covers/01M4HXNQJBQWQ4PBP8RMMN7H82.webp',
                'activity_date' => Carbon::now()->subDays(10),
                'location' => 'Balai Desa Damai',
                'status' => 'published',
            ],
            [
                'title' => 'Penyuluhan Kesehatan Ibu dan Anak',
                'slug' => 'penyuluhan-kesehatan-ibu-dan-anak',
                'category_id' => $catSosial->id,
                'user_id' => $admin->id,
                'content' => '<p>Penyuluhan kesehatan ibu hamil dan balita oleh Bidan Desa dan Puskesmas Pembantu. Materi: imunisasi lengkap, pola makan seimbang, deteksi dini stunting, dan KB. Hadir 50 ibu hamil dan balita beserta orang tua.</p>',
                'excerpt' => 'Penyuluhan rutin kesehatan ibu dan anak untuk mencegah stunting di Desa Damai.',
                'cover_image' => 'activities/covers/01M55G6YRCFN3DBC5ZGFX1HB1P.webp',
                'activity_date' => Carbon::now()->subDays(15),
                'location' => 'Posyandu Dusun 2',
                'status' => 'published',
            ],
            [
                'title' => 'Lomba 17 Agustus: Panjat Pinang dan Balap Karung',
                'slug' => 'lomba-17-agustus-panjat-pinang-balap-karung',
                'category_id' => $catBudaya->id,
                'user_id' => $admin->id,
                'content' => '<p>Perayaan HUT RI ke-79 diisi lomba tradisional panjat pinang, balap karung, makan kerupuk, dan tarik tambang. Diikuti warga seluruh dusun dengan antusias tinggi. Hadiah disponsori oleh BUMDes dan toko-toko lokal.</p>',
                'excerpt' => 'Perayaan kemerdekaan dengan lomba tradisional seru di Lapangan Desa.',
                'cover_image' => null,
                'activity_date' => Carbon::parse('2024-08-17'),
                'location' => 'Lapangan Desa Damai',
                'status' => 'published',
            ],
            [
                'title' => 'Sosialisasi Program Bantuan Siswa Miskin (BSM)',
                'slug' => 'sosialisasi-program-bantuan-siswa-miskin-bsm',
                'category_id' => $catPendidikan->id,
                'user_id' => $admin->id,
                'content' => '<p>Sosialisasi pendaftaran BSM bagi siswa SD/SMP/SMA dari keluarga kurang mampu. Dilaksanakan di SDN 1 Damai dihadiri orang tua/wali siswa. Tim dari Dinas Pendidikan Kabupaten membimbing proses pendaftaran online.</p>',
                'excerpt' => 'Bantuan pendidikan bagi siswa kurang mampu agar tetap bisa sekolah.',
                'cover_image' => null,
                'activity_date' => Carbon::now()->subDays(20),
                'location' => 'SDN 1 Damai',
                'status' => 'published',
            ],
            [
                'title' => 'Peresmian Jalan Desa Dusun 3',
                'slug' => 'peresmian-jalan-desa-dusun-3',
                'category_id' => $catPembangunan->id,
                'user_id' => $admin->id,
                'content' => '<p>Peresmian pembangunan jalan beton panjang 500 meter di Dusun 3 yang dibiayai dari Dana Desa (ADD) tahun 2024. Jalan ini mempermudah akses warga ke sawah dan ke jalan raya utama. Diresmikan oleh Kepala Desa bersama Camat dan perwakilan DPRD.</p>',
                'excerpt' => 'Pembangunan jalan beton 500m di Dusun 3 dari Dana Desa 2024.',
                'cover_image' => null,
                'activity_date' => Carbon::now()->subDays(30),
                'location' => 'Dusun 3, Desa Damai',
                'status' => 'published',
            ],
        ];

        foreach ($activities as $activityData) {
            Activity::firstOrCreate(
                ['title' => $activityData['title']],
                $activityData
            );
        }

        // 5. Lembaga Desa
        $lpm = Institution::firstOrCreate(
            ['name' => 'LPM (Lembaga Pemberdayaan Masyarakat)'],
            [
                'slug' => 'lpm-lembaga-pemberdayaan-masyarakat',
                'description' => 'Lembaga yang menjadi mitra kerja pemerintah desa dalam merencanakan dan melaksanakan pembangunan desa.',
                'order' => 1,
            ]
        );

        InstitutionMember::firstOrCreate(
            ['name' => 'Ahmad Sujono'],
            [
                'institution_id' => $lpm->id,
                'position' => 'Ketua LPM',
                'order' => 1,
            ]
        );

        // 6. Point Of Interest (Peta)
        PointOfInterest::firstOrCreate(
            ['name' => 'Kantor Kepala Desa Damai'],
            [
                'category' => 'kantor_desa',
                'address' => 'Jl. Raya Desa No. 1',
                'hamlet' => 'Dusun Krajan',
                'latitude' => -7.250445,
                'longitude' => 112.768845,
                'description' => 'Pusat pelayanan administrasi warga desa.',
            ]
        );

        PointOfInterest::firstOrCreate(
            ['name' => 'Puskesmas Pembantu (Pustu)'],
            [
                'category' => 'fasilitas_umum',
                'address' => 'Jl. Merdeka No. 45',
                'hamlet' => 'Dusun 2',
                'latitude' => -7.255555,
                'longitude' => 112.760000,
                'description' => 'Fasilitas kesehatan tingkat pertama untuk warga desa.',
            ]
        );

        // 7. Kontak Penting
        ImportantContact::firstOrCreate(
            ['name' => 'Bidan Siti Aminah'],
            [
                'position' => 'Bidan Desa',
                'phone_number' => '081234567890',
                'is_approved_for_publication' => true,
            ]
        );

        ImportantContact::firstOrCreate(
            ['name' => 'Bapak Budi (Rahasia)'],
            [
                'position' => 'Kasi Pemerintahan',
                'phone_number' => '089999999999',
                'is_approved_for_publication' => false,
            ]
        );
    }
}
