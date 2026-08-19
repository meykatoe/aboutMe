<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleSwitchingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_switching_locale_updates_session_and_current_request(): void
    {
        $response = $this->post('/locale/en');

        $response->assertRedirect();
        $this->assertSame('en', session('locale'));
    }

    public function test_authenticated_user_switching_locale_persists_to_their_account(): void
    {
        $user = User::factory()->create(['locale' => null]);

        $this->actingAs($user)->post('/locale/en');

        $this->assertSame('en', $user->fresh()->locale);
    }

    public function test_unsupported_locale_is_rejected(): void
    {
        $this->post('/locale/fr')->assertNotFound();
    }

    public function test_stored_user_locale_is_applied_on_subsequent_requests(): void
    {
        $user = User::factory()->create(['locale' => 'en']);

        $this->actingAs($user)->get('/dashboard');

        $this->assertSame('en', app()->getLocale());
    }

    public function test_guest_browser_language_preference_is_honored(): void
    {
        $this->withHeaders(['Accept-Language' => 'zh-TW,zh;q=0.9'])->get('/login');

        $this->assertSame('zh_TW', app()->getLocale());

        $this->withHeaders(['Accept-Language' => 'en-US,en;q=0.9'])->get('/login');

        $this->assertSame('en', app()->getLocale());
    }
}
