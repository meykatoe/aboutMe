<?php

namespace Tests\Feature;

use App\Models\SocialLink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class SocialLinksManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_add_a_social_link(): void
    {
        $user = User::factory()->create();

        Volt::actingAs($user)
            ->test('profile.manage-social-links')
            ->set('url', 'https://twitter.com/laravelphp')
            ->call('addSocialLink')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('social_links', [
            'user_id' => $user->id,
            'url' => 'https://twitter.com/laravelphp',
        ]);
    }

    public function test_url_must_be_a_valid_url(): void
    {
        $user = User::factory()->create();

        Volt::actingAs($user)
            ->test('profile.manage-social-links')
            ->set('url', 'not-a-url')
            ->call('addSocialLink')
            ->assertHasErrors(['url']);

        $this->assertDatabaseMissing('social_links', ['user_id' => $user->id]);
    }

    public function test_url_must_use_http_or_https_scheme(): void
    {
        $user = User::factory()->create();

        Volt::actingAs($user)
            ->test('profile.manage-social-links')
            ->set('url', 'ftp://example.com/file')
            ->call('addSocialLink')
            ->assertHasErrors(['url']);

        $this->assertDatabaseMissing('social_links', ['user_id' => $user->id]);
    }

    public function test_user_can_delete_their_own_social_link(): void
    {
        $user = User::factory()->create();
        $link = SocialLink::factory()->for($user)->create();

        Volt::actingAs($user)
            ->test('profile.manage-social-links')
            ->call('deleteSocialLink', $link->id);

        $this->assertDatabaseMissing('social_links', ['id' => $link->id]);
    }

    public function test_user_cannot_delete_another_users_social_link(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $link = SocialLink::factory()->for($owner)->create();

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        Volt::actingAs($intruder)
            ->test('profile.manage-social-links')
            ->call('deleteSocialLink', $link->id);
    }

    public function test_add_button_warns_once_eight_links_already_exist(): void
    {
        $user = User::factory()->create();
        SocialLink::factory()->for($user)->count(8)->create();

        $withWarning = Volt::actingAs($user)->test('profile.manage-social-links');
        $withWarning->assertSee('太多可能會讓版面不好看');

        $secondUser = User::factory()->create();
        SocialLink::factory()->for($secondUser)->count(7)->create();

        $withoutWarning = Volt::actingAs($secondUser)->test('profile.manage-social-links');
        $withoutWarning->assertDontSee('太多可能會讓版面不好看');
    }
}
