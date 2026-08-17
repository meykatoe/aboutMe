<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicProfilePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_a_users_public_profile_page(): void
    {
        User::factory()->create([
            'name' => 'Jane Doe',
            'username' => 'janedoe',
        ]);

        $response = $this->get('/janedoe');

        $response
            ->assertOk()
            ->assertSee('Jane Doe')
            ->assertSee('@janedoe');
    }

    public function test_unknown_username_returns_404(): void
    {
        $response = $this->get('/no-such-user');

        $response->assertNotFound();
    }
}
