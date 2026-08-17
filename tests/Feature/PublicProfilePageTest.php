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

    public function test_public_profile_page_shows_bio_when_set(): void
    {
        User::factory()->create([
            'username' => 'withbio',
            'bio' => '這是我的自我介紹。',
        ]);

        $this->get('/withbio')->assertSee('這是我的自我介紹。');
    }

    public function test_public_profile_page_shows_placeholder_when_bio_is_empty(): void
    {
        User::factory()->create([
            'username' => 'nobio',
            'bio' => null,
        ]);

        $this->get('/nobio')->assertSee('這位使用者還沒有設定個人介紹。');
    }

    public function test_unknown_username_returns_404(): void
    {
        $response = $this->get('/no-such-user');

        $response->assertNotFound();
    }
}
