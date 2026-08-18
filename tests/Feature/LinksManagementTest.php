<?php

namespace Tests\Feature;

use App\Models\Link;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class LinksManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_links_page(): void
    {
        $this->get('/links')->assertRedirect('/login');
    }

    public function test_user_can_add_a_link(): void
    {
        $user = User::factory()->create();

        Volt::actingAs($user)
            ->test('links.manage')
            ->set('title', 'My Website')
            ->set('url', 'https://example.com')
            ->set('description', 'A link to my site')
            ->call('addLink')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('links', [
            'user_id' => $user->id,
            'title' => 'My Website',
            'url' => 'https://example.com',
        ]);
    }

    public function test_url_must_be_a_valid_url(): void
    {
        $user = User::factory()->create();

        Volt::actingAs($user)
            ->test('links.manage')
            ->set('title', 'Bad Link')
            ->set('url', 'not-a-url')
            ->call('addLink')
            ->assertHasErrors(['url']);

        $this->assertDatabaseMissing('links', ['title' => 'Bad Link']);
    }

    public function test_url_must_use_http_or_https_scheme(): void
    {
        $user = User::factory()->create();

        Volt::actingAs($user)
            ->test('links.manage')
            ->set('title', 'FTP Link')
            ->set('url', 'ftp://example.com/file')
            ->call('addLink')
            ->assertHasErrors(['url']);

        $this->assertDatabaseMissing('links', ['title' => 'FTP Link']);
    }

    public function test_adding_links_is_rate_limited(): void
    {
        $user = User::factory()->create();
        $component = Volt::actingAs($user)->test('links.manage');

        for ($i = 0; $i < 10; $i++) {
            $component
                ->set('title', "Link {$i}")
                ->set('url', 'https://example.com')
                ->call('addLink');
        }

        $this->assertSame(10, $user->links()->count());

        $component
            ->set('title', 'One Too Many')
            ->set('url', 'https://example.com')
            ->call('addLink')
            ->assertHasErrors(['title']);

        $this->assertSame(10, $user->links()->count());
    }

    public function test_user_can_update_their_own_link(): void
    {
        $user = User::factory()->create();
        $link = Link::factory()->for($user)->create(['title' => 'Old Title']);

        Volt::actingAs($user)
            ->test('links.manage')
            ->call('edit', $link->id)
            ->set('title', 'New Title')
            ->call('updateLink')
            ->assertHasNoErrors();

        $this->assertSame('New Title', $link->fresh()->title);
    }

    public function test_user_can_delete_their_own_link(): void
    {
        $user = User::factory()->create();
        $link = Link::factory()->for($user)->create();

        Volt::actingAs($user)
            ->test('links.manage')
            ->call('deleteLink', $link->id);

        $this->assertDatabaseMissing('links', ['id' => $link->id]);
    }

    public function test_user_cannot_delete_another_users_link(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $link = Link::factory()->for($owner)->create();

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        Volt::actingAs($intruder)
            ->test('links.manage')
            ->call('deleteLink', $link->id);
    }

    public function test_user_can_reorder_links(): void
    {
        $user = User::factory()->create();
        $first = Link::factory()->for($user)->create(['position' => 1]);
        $second = Link::factory()->for($user)->create(['position' => 2]);

        Volt::actingAs($user)
            ->test('links.manage')
            ->call('moveUp', $second->id);

        $this->assertSame(1, $second->fresh()->position);
        $this->assertSame(2, $first->fresh()->position);
    }
}
