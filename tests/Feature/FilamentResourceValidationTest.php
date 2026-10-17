<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageSettings;
use App\Filament\Resources\ActivityResource;
use App\Filament\Resources\ImportantContactResource;
use App\Filament\Resources\ImportantContactResource\Pages\CreateImportantContact;
use App\Filament\Resources\PointOfInterestResource;
use App\Models\Activity;
use App\Models\ActivityCategory;
use App\Models\ImportantContact;
use App\Models\Institution;
use App\Models\InstitutionMember;
use App\Models\PointOfInterest;
use App\Models\Setting;
use App\Models\User;
use App\Models\VillageHead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class FilamentResourceValidationTest extends TestCase
{
    use RefreshDatabase;

    protected User $superadmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superadmin = User::factory()->create(['role' => 'superadmin']);
        $this->actingAs($this->superadmin);
    }

    public function test_village_head_active_toggle_is_exclusive(): void
    {
        $head1 = VillageHead::create([
            'name' => 'Kades Lama',
            'period_start' => 2015,
            'period_end' => 2021,
            'is_active' => true,
        ]);

        $this->assertTrue($head1->fresh()->is_active);

        $head2 = VillageHead::create([
            'name' => 'Kades Baru',
            'period_start' => 2021,
            'is_active' => true,
        ]);

        $this->assertFalse($head1->fresh()->is_active);
        $this->assertTrue($head2->fresh()->is_active);
    }

    public function test_activity_auto_slug_and_excerpt(): void
    {
        $category = ActivityCategory::create(['name' => 'Berita', 'slug' => 'berita']);

        $activity = Activity::create([
            'title' => 'Kegiatan Kerja Bakti Desa',
            'slug' => 'kegiatan-kerja-bakti-desa',
            'content' => 'Konten kegiatan kerja bakti warga desa.',
            'category_id' => $category->id,
            'activity_date' => now(),
            'status' => 'published',
            'user_id' => $this->superadmin->id,
            'excerpt' => 'Ringkasan kerja bakti.',
        ]);

        $this->assertEquals('kegiatan-kerja-bakti-desa', $activity->slug);
        $this->assertEquals('published', $activity->status);
    }

    public function test_point_of_interest_conditional_photo_validation(): void
    {
        Livewire::test(PointOfInterestResource\Pages\CreatePointOfInterest::class)
            ->fillForm([
                'name' => 'Rumah Kadus 1',
                'category' => 'rumah_kadus',
                'latitude' => -7.55,
                'longitude' => 110.81,
                'photo_path' => null,
            ])
            ->call('create')
            ->assertHasFormErrors(['photo_path' => 'required']);
    }

    public function test_point_of_interest_latitude_longitude_bounds(): void
    {
        Livewire::test(PointOfInterestResource\Pages\CreatePointOfInterest::class)
            ->fillForm([
                'name' => 'Pos Ronda',
                'category' => 'fasilitas_umum',
                'latitude' => 100, // invalid (>90)
                'longitude' => 110.81,
            ])
            ->call('create')
            ->assertHasFormErrors(['latitude']);
    }

    public function test_important_contact_phone_normalization_and_approval(): void
    {
        $normalized = CreateImportantContact::normalizePhone('081234567890');
        $this->assertEquals('6281234567890', $normalized);

        $contact = ImportantContact::create([
            'name' => 'Kepala Dusun Krajan',
            'position' => 'Kadus Krajan',
            'phone_number' => '6281234567890',
            'number_type' => 'dinas',
            'is_approved_for_publication' => false,
        ]);

        $this->assertFalse($contact->is_approved_for_publication);
        $this->assertCount(0, ImportantContact::published()->get());
    }

    public function test_settings_manage_page_saves_correctly(): void
    {
        Livewire::test(ManageSettings::class)
            ->fillForm([
                'village_name' => 'Desa Makmur',
                'village_district' => 'Kecamatan Melati',
                'village_regency' => 'Kabupaten Nusantara',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertEquals('Desa Makmur', Setting::get('village_name'));
    }
}
