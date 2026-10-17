<?php

namespace Tests\Feature;

use App\Models\ImportantContact;
use App\Models\PointOfInterest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class ModelValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_important_contacts_approval_defaults_to_false(): void
    {
        $contact = ImportantContact::create([
            'name' => 'Budi',
            'position' => 'Kepala Dusun I',
            'phone_number' => '081234567890',
        ]);

        $this->assertFalse($contact->is_approved_for_publication);
        $this->assertEquals(0, ImportantContact::published()->count());

        $contact->update(['is_approved_for_publication' => true]);
        $this->assertEquals(1, ImportantContact::published()->count());
    }

    public function test_point_of_interest_latitude_and_longitude_boundaries(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new PointOfInterest([
            'name' => 'Pos Pantau',
            'category' => 'fasilitas_umum',
            'latitude' => 95.5,
            'longitude' => 110.0,
        ]);
    }
}
