<?php

namespace Tests\Feature;

use App\Models\FacultyProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use DatabaseMigrations;

    public function test_faculty_profile_page_is_displayed(): void
    {
        $user = User::factory()->create(['role' => 'faculty']);

        $response = $this
            ->actingAs($user)
            ->get('/faculty/profile');

        $response->assertOk();
    }

    public function test_faculty_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create(['role' => 'faculty']);

        $response = $this
            ->actingAs($user)
            ->put('/faculty/profile', [
                'bio' => 'Focused on pedagogy',
                'department' => 'Computer Science',
                'designation' => 'Assistant Professor',
                'skills' => 'php, laravel, mongodb',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $profile = FacultyProfile::firstWhere('user_id', (string) $user->id);

        $this->assertNotNull($profile);
        $this->assertSame('Focused on pedagogy', $profile?->bio);
        $this->assertSame('Computer Science', $profile?->department);
        $this->assertSame(['php', 'laravel', 'mongodb'], $profile?->skills);
    }

    public function test_profile_update_creates_profile_when_missing(): void
    {
        $user = User::factory()->create(['role' => 'faculty']);

        $response = $this
            ->actingAs($user)
            ->put('/faculty/profile', [
                'bio' => 'Data-driven educator',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertNotNull(FacultyProfile::firstWhere('user_id', (string) $user->id));
    }
}

