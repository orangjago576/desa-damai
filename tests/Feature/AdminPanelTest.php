<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_seeder_creates_superadmin_from_env(): void
    {
        $this->seed(SuperAdminSeeder::class);

        $this->assertDatabaseHas('users', [
            'email' => env('SUPERADMIN_EMAIL', 'superadmin@desadamai.id'),
            'role' => 'superadmin',
        ]);
    }

    public function test_superadmin_can_access_all_panels_including_settings_and_users(): void
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $this->actingAs($superadmin);

        $this->get('/admin')->assertSuccessful();
        $this->get('/admin/users')->assertSuccessful();
        $this->get('/admin/manage-settings')->assertSuccessful();
    }

    public function test_regular_admin_cannot_access_settings_and_user_management(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        // Dapat mengakses dashboard dan konten
        $this->get('/admin')->assertSuccessful();
        $this->get('/admin/activities')->assertSuccessful();
        $this->get('/admin/important-contacts')->assertSuccessful();

        // TIDAK DAPAT mengakses manajemen pengguna & pengaturan
        $this->get('/admin/users')->assertForbidden();
        $this->get('/admin/manage-settings')->assertForbidden();
    }

    public function test_login_page_renders_village_branding_without_errors(): void
    {
        $response = $this->get('/admin/login');
        $response->assertSuccessful();
        $response->assertSee(env('VILLAGE_NAME', 'Desa Damai'));
    }
}
