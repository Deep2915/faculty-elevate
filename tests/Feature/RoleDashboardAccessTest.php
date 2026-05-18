<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class RoleDashboardAccessTest extends TestCase
{
    use DatabaseMigrations;

    public function test_dashboard_redirects_to_admin_dashboard_for_admin_role(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertRedirect(route('admin.dashboard', absolute: false));
    }

    public function test_dashboard_redirects_to_hod_dashboard_for_hod_role(): void
    {
        $user = User::factory()->create(['role' => 'hod']);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertRedirect(route('hod.dashboard', absolute: false));
    }

    public function test_dashboard_redirects_to_faculty_dashboard_for_faculty_role(): void
    {
        $user = User::factory()->create(['role' => 'faculty']);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertRedirect(route('faculty.dashboard', absolute: false));
    }

    public function test_each_role_is_forbidden_from_other_role_dashboards(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $hod = User::factory()->create(['role' => 'hod']);
        $faculty = User::factory()->create(['role' => 'faculty']);

        $this->actingAs($admin)->get('/hod/dashboard')->assertForbidden();
        $this->actingAs($admin)->get('/faculty/dashboard')->assertForbidden();

        $this->actingAs($hod)->get('/admin/dashboard')->assertForbidden();
        $this->actingAs($hod)->get('/faculty/dashboard')->assertForbidden();

        $this->actingAs($faculty)->get('/admin/dashboard')->assertForbidden();
        $this->actingAs($faculty)->get('/hod/dashboard')->assertForbidden();
    }
}
