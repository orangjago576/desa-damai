<?php

namespace Tests\Feature;

use App\Filament\Resources\PointOfInterestResource\Pages\CreatePointOfInterest;
use App\Models\PointOfInterest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PointOfInterestMapTest extends TestCase
{
    use RefreshDatabase;

    protected User $superadmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superadmin = User::factory()->create(['role' => 'superadmin']);
    }

    public function test_admin_rejects_coordinates_outside_regency_bounding_box(): void
    {
        $this->actingAs($this->superadmin);

        // Latitude di luar bounding box (-5.0 is out of -8.5..-6.0)
        Livewire::test(CreatePointOfInterest::class)
            ->fillForm([
                'name' => 'Lokasi Luar Daerah',
                'category' => 'kantor_desa',
                'latitude' => -5.0,
                'longitude' => 110.818130,
            ])
            ->call('create')
            ->assertHasFormErrors(['latitude']);
    }

    public function test_admin_accepts_valid_regency_coordinates(): void
    {
        $this->actingAs($this->superadmin);

        Livewire::test(CreatePointOfInterest::class)
            ->fillForm([
                'name' => 'Kantor Kepala Desa',
                'category' => 'kantor_desa',
                'latitude' => -7.550762,
                'longitude' => 110.818130,
                'address' => 'Jl. Raya Desa No. 1',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('points_of_interest', [
            'name' => 'Kantor Kepala Desa',
            'latitude' => -7.550762,
            'longitude' => 110.818130,
        ]);
    }

    public function test_public_map_page_renders_successfully_with_google_maps_links(): void
    {
        PointOfInterest::create([
            'name' => 'Kantor Desa Damai',
            'category' => 'kantor_desa',
            'latitude' => -7.550762,
            'longitude' => 110.818130,
            'address' => 'Jl. Balai Desa No. 01',
        ]);

        $response = $this->get('/peta');

        $response->assertStatus(200);
        $response->assertSee('Peta Lokasi Penting Desa');
        $response->assertSee('Kantor Desa Damai');
        $response->assertSee('https://www.google.com/maps?q=-7.550762,110.81813', false);
    }
}
