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

        // 4. Kegiatan Desa
        Activity::firstOrCreate(
            ['title' => 'Gotong Royong Pembersihan Irigasi'],
            [
                'slug' => 'gotong-royong-pembersihan-irigasi',
                'category_id' => $catSosial->id,
                'user_id' => $admin->id,
                'content' => '<p>Kegiatan gotong royong rutin membersihkan saluran air (irigasi) di Dusun 1 untuk persiapan musim penghujan.</p>',
                'excerpt' => 'Warga Dusun 1 bersama perangkat desa melaksanakan kerja bakti pembersihan saluran irigasi.',
                'activity_date' => Carbon::now()->subDays(2),
                'location' => 'Dusun 1, Desa Damai',
                'status' => 'published',
            ]
        );

        Activity::firstOrCreate(
            ['title' => 'Musrenbangdes Tahun 2026'],
            [
                'slug' => 'musrenbangdes-tahun-2026',
                'category_id' => $catPembangunan->id,
                'user_id' => $admin->id,
                'content' => '<p>Musyawarah Perencanaan Pembangunan Desa (Musrenbangdes) dihadiri oleh BPD, perangkat desa, dan perwakilan warga.</p>',
                'excerpt' => 'Penetapan skala prioritas pembangunan tahun 2027 melalui Musrenbangdes.',
                'activity_date' => Carbon::now()->subDays(5),
                'location' => 'Balai Desa Damai',
                'status' => 'published',
            ]
        );

        // 5. Lembaga Desa
        $lpm = Institution::firstOrCreate(
            ['name' => 'LPM (Lembaga Pemberdayaan Masyarakat)'],
            [
                'description' => 'Lembaga yang menjadi mitra kerja pemerintah desa dalam merencanakan dan melaksanakan pembangunan desa.',
                'order' => 1,
            ]
        );

        InstitutionMember::firstOrCreate(
            ['name' => 'Ahmad Sujono'],
            [
                'institution_id' => $lpm->id,
                'position' => 'Ketua LPM',
                'sort_order' => 1,
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
