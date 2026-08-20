<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class TwoFactorAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_enable_two_factor_authentication(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $component = Volt::test('profile.two-factor-authentication');
        $component->call('enableTwoFactorAuthentication');

        $user->refresh();

        $this->assertNotNull($user->two_factor_secret);
        $this->assertNotNull($user->two_factor_recovery_codes);
        $this->assertNull($user->two_factor_confirmed_at);
    }

    public function test_user_can_confirm_two_factor_authentication_with_valid_code(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $component = Volt::test('profile.two-factor-authentication');
        $component->call('enableTwoFactorAuthentication');

        $secret = $user->refresh()->two_factor_secret;
        $code = (new Google2FA)->getCurrentOtp($secret);

        $component->set('code', $code)->call('confirmTwoFactorAuthentication');

        $component->assertHasNoErrors();

        $this->assertTrue($user->refresh()->hasEnabledTwoFactorAuthentication());
    }

    public function test_confirming_two_factor_authentication_fails_with_invalid_code(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $component = Volt::test('profile.two-factor-authentication');
        $component->call('enableTwoFactorAuthentication');

        $component->set('code', '000000')->call('confirmTwoFactorAuthentication');

        $component->assertHasErrors('code');

        $this->assertFalse($user->refresh()->hasEnabledTwoFactorAuthentication());
    }

    public function test_non_admin_user_can_disable_two_factor_authentication(): void
    {
        $user = User::factory()->withTwoFactorEnabled()->create();

        $this->actingAs($user);

        $component = Volt::test('profile.two-factor-authentication')
            ->set('current_password', 'password')
            ->call('disableTwoFactorAuthentication');

        $component->assertHasNoErrors();

        $this->assertFalse($user->refresh()->hasEnabledTwoFactorAuthentication());
    }

    public function test_admin_can_not_disable_two_factor_authentication(): void
    {
        $user = User::factory()->admin()->withTwoFactorEnabled()->create();

        $this->actingAs($user);

        Volt::test('profile.two-factor-authentication')
            ->set('current_password', 'password')
            ->call('disableTwoFactorAuthentication')
            ->assertForbidden();

        $this->assertTrue($user->refresh()->hasEnabledTwoFactorAuthentication());
    }

    public function test_user_can_regenerate_recovery_codes(): void
    {
        $user = User::factory()->withTwoFactorEnabled()->create();
        $originalCodes = $user->two_factor_recovery_codes;

        $this->actingAs($user);

        Volt::test('profile.two-factor-authentication')
            ->set('current_password', 'password')
            ->call('regenerateRecoveryCodes')
            ->assertHasNoErrors();

        $this->assertNotEquals($originalCodes, $user->refresh()->two_factor_recovery_codes);
    }
}
