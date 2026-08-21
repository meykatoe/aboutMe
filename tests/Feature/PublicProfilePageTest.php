<?php

namespace Tests\Feature;

use App\Models\Link;
use App\Models\SocialLink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
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

        $this->withHeaders(['Accept-Language' => 'zh-TW,zh;q=0.9'])
            ->get('/nobio')
            ->assertSee('這位使用者還沒有設定個人介紹。');
    }

    public function test_public_profile_page_shows_avatar_image_when_set(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'username' => 'hasavatar',
            'avatar_path' => 'avatars/example.jpg',
        ]);

        $this->get('/hasavatar')->assertSee($user->avatar_url, false);
    }

    public function test_public_profile_page_applies_custom_background_color(): void
    {
        User::factory()->create([
            'username' => 'colorbg',
            'background_color' => '#112233',
        ]);

        $this->get('/colorbg')->assertSee('#112233', false);
    }

    public function test_public_profile_page_shows_background_images_when_set(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'username' => 'imagebg',
            'background_image_pc_path' => 'backgrounds/pc/example.jpg',
            'background_image_mobile_path' => 'backgrounds/mobile/example.jpg',
        ]);

        $response = $this->get('/imagebg');

        $response
            ->assertSee($user->background_image_pc_url, false)
            ->assertSee($user->background_image_mobile_url, false);
    }

    public function test_public_profile_page_lists_users_links_in_order(): void
    {
        $user = User::factory()->create(['username' => 'linky']);

        Link::factory()->for($user)->create(['title' => 'Second Link', 'position' => 2]);
        Link::factory()->for($user)->create(['title' => 'First Link', 'position' => 1]);

        $response = $this->get('/linky');

        $response->assertSeeInOrder(['First Link', 'Second Link']);
    }

    public function test_public_profile_page_shows_social_link_icons(): void
    {
        $user = User::factory()->create(['username' => 'social']);

        SocialLink::factory()->for($user)->create(['url' => 'https://github.com/laravel']);

        $response = $this->get('/social');

        $response
            ->assertOk()
            ->assertSee('https://github.com/laravel', false);
    }

    public function test_visiting_a_profile_page_increments_its_view_count(): void
    {
        $user = User::factory()->create(['username' => 'viewed', 'profile_views' => 0]);

        $this->get('/viewed');
        $this->get('/viewed');

        $this->assertSame(2, $user->fresh()->profile_views);
    }

    public function test_unknown_username_returns_404(): void
    {
        $response = $this->get('/no-such-user');

        $response->assertNotFound();
    }

    public function test_guest_gets_404_for_private_profile(): void
    {
        User::factory()->create([
            'username' => 'privateuser',
            'is_public' => false,
        ]);

        $this->get('/privateuser')->assertNotFound();
    }

    public function test_other_logged_in_user_gets_404_for_private_profile(): void
    {
        User::factory()->create([
            'username' => 'privateuser',
            'is_public' => false,
        ]);
        $other = User::factory()->create();

        $this->actingAs($other)
            ->get('/privateuser')
            ->assertNotFound();
    }

    public function test_public_profile_page_includes_open_graph_meta_tags(): void
    {
        User::factory()->create([
            'name' => 'Jane Doe',
            'username' => 'janedoe',
            'bio' => '這是我的自我介紹。',
        ]);

        $response = $this->get('/janedoe');

        $response
            ->assertSee('<meta property="og:title" content="Jane Doe (@janedoe)">', false)
            ->assertSee('<meta property="og:description" content="這是我的自我介紹。">', false)
            ->assertSee('<meta property="og:url" content="'.route('profile.show', ['username' => 'janedoe']).'">', false)
            ->assertSee('<meta name="twitter:title" content="Jane Doe (@janedoe)">', false);
    }

    public function test_public_profile_page_uses_fallback_description_when_bio_is_empty(): void
    {
        User::factory()->create([
            'name' => 'Jane Doe',
            'username' => 'nobio2',
            'bio' => null,
        ]);

        $this->withHeaders(['Accept-Language' => 'zh-TW,zh;q=0.9'])
            ->get('/nobio2')
            ->assertSee('<meta property="og:description" content="Jane Doe 在 '.config('app.name').' 的個人頁面">', false);
    }

    public function test_public_profile_page_includes_og_image_when_avatar_is_set(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'username' => 'avatarog',
            'avatar_path' => 'avatars/example.jpg',
        ]);

        $this->get('/avatarog')
            ->assertSee('<meta property="og:image" content="'.$user->avatar_url.'">', false)
            ->assertSee('<meta name="twitter:image" content="'.$user->avatar_url.'">', false);
    }

    public function test_public_profile_page_omits_og_image_when_avatar_is_not_set(): void
    {
        $user = User::factory()->create([
            'username' => 'noavatarog',
            'avatar_path' => null,
        ]);

        $this->get('/noavatarog')
            ->assertDontSee('property="og:image"', false)
            ->assertDontSee('name="twitter:image"', false);
    }

    public function test_owner_can_view_own_private_profile_while_logged_in(): void
    {
        $owner = User::factory()->create([
            'username' => 'privateuser',
            'is_public' => false,
        ]);

        $this->actingAs($owner)
            ->get('/privateuser')
            ->assertOk();
    }
}
