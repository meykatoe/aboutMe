<?php

namespace Tests\Feature;

use App\Models\Link;
use App\Models\SocialLink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Volt;
use Tests\TestCase;

class ExportProfileDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_export_their_profile_page_as_html(): void
    {
        $user = User::factory()->create([
            'name' => 'Jane Doe',
            'username' => 'janedoe',
            'bio' => '這是我的自我介紹。',
        ]);

        Link::factory()->for($user)->create([
            'title' => '我的部落格',
            'url' => 'https://example.com/blog',
            'description' => '分享一些筆記',
        ]);

        SocialLink::factory()->for($user)->create([
            'url' => 'https://twitter.com/janedoe',
        ]);

        $component = Volt::actingAs($user)
            ->test('profile.export-data')
            ->call('export');

        $component->assertFileDownloaded();

        $content = base64_decode(data_get($component->effects, 'download.content'));

        $this->assertStringContainsString('Jane Doe', $content);
        $this->assertStringContainsString('@janedoe', $content);
        $this->assertStringContainsString('這是我的自我介紹。', $content);
        $this->assertStringContainsString('我的部落格', $content);
        $this->assertStringContainsString('https://example.com/blog', $content);
        $this->assertStringContainsString('分享一些筆記', $content);
        $this->assertStringContainsString('https://twitter.com/janedoe', $content);
    }

    public function test_export_embeds_avatar_as_a_data_uri_for_offline_viewing(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'username' => 'withavatar',
            'avatar_path' => 'avatars/test.jpg',
        ]);

        Storage::disk('public')->put(
            'avatars/test.jpg',
            UploadedFile::fake()->image('avatar.jpg')->get()
        );

        $component = Volt::actingAs($user)
            ->test('profile.export-data')
            ->call('export');

        $content = base64_decode(data_get($component->effects, 'download.content'));

        $this->assertStringContainsString('data:image/jpeg;base64,', $content);
        $this->assertStringNotContainsString('avatars/test.jpg', $content);
    }
}
