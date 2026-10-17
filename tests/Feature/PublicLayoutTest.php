<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_homepage_renders_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Portal Resmi Pemerintah Desa');
        $response->assertSee('Beranda');
        $response->assertSee('Kegiatan Desa');
        $response->assertSee('Peta Lokasi');
    }

    public function test_custom_404_error_page_renders_in_indonesian(): void
    {
        $response = $this->get('/halaman-yang-tidak-ada-123');

        $response->assertStatus(404);
        $response->assertSee('Halaman Tidak Ditemukan');
        $response->assertSee('Kembali ke Beranda');
    }
}
