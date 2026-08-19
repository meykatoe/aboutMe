<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_the_users_public_profile_url_and_qr_code(): void
    {
        $user = User::factory()->create(['username' => 'meykatoe']);

        $response = $this->actingAs($user)->get('/dashboard');

        $profileUrl = route('profile.show', ['username' => 'meykatoe']);

        $response
            ->assertOk()
            ->assertSee($profileUrl)
            ->assertSee('data-qrcode-url="'.$profileUrl.'"', false);
    }

    public function test_guests_cannot_view_the_dashboard(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }
}
