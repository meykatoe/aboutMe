<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class TwoFactorChallengeTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_redirects_to_challenge_when_two_factor_is_enabled(): void
    {
        $user = User::factory()->withTwoFactorEnabled()->create();

        $component = Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'password');

        $component->call('login');

        $component
            ->assertHasNoErrors()
            ->assertRedirect(route('two-factor.login', absolute: false));

        $this->assertGuest();
    }

    public function test_user_can_complete_login_with_valid_code(): void
    {
        $user = User::factory()->withTwoFactorEnabled()->create();

        Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'password')
            ->call('login');

        $code = (new Google2FA)->getCurrentOtp($user->two_factor_secret);

        $component = Volt::test('pages.auth.two-factor-challenge')
            ->set('code', $code)
            ->call('authenticate');

        $component
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticatedAs($user);
    }

    public function test_user_can_complete_login_with_recovery_code(): void
    {
        $user = User::factory()->withTwoFactorEnabled()->create();
        $recoveryCode = $user->two_factor_recovery_codes[0];

        Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'password')
            ->call('login');

        $component = Volt::test('pages.auth.two-factor-challenge')
            ->set('useRecoveryCode', true)
            ->set('recovery_code', $recoveryCode)
            ->call('authenticate');

        $component
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticatedAs($user);

        $this->assertNotContains($recoveryCode, $user->refresh()->two_factor_recovery_codes);
    }

    public function test_challenge_fails_with_invalid_code(): void
    {
        $user = User::factory()->withTwoFactorEnabled()->create();

        Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'password')
            ->call('login');

        $component = Volt::test('pages.auth.two-factor-challenge')
            ->set('code', '000000')
            ->call('authenticate');

        $component->assertHasErrors('code');

        $this->assertGuest();
    }

    public function test_admin_without_two_factor_is_redirected_to_profile(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin);

        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('profile'));
    }

    public function test_admin_with_two_factor_can_access_dashboard(): void
    {
        $admin = User::factory()->admin()->withTwoFactorEnabled()->create();

        $this->actingAs($admin);

        $response = $this->get(route('dashboard'));

        $response->assertOk();
    }

    public function test_regular_user_without_two_factor_can_access_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get(route('dashboard'));

        $response->assertOk();
    }
}
