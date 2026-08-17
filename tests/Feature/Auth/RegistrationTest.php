<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response
            ->assertOk()
            ->assertSeeVolt('pages.auth.register');
    }

    public function test_new_users_can_register(): void
    {
        $component = Volt::test('pages.auth.register')
            ->set('name', 'Test User')
            ->set('username', 'testuser')
            ->set('email', 'test@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password');

        $component->call('register');

        $component->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();
    }

    public function test_registration_requires_unique_username(): void
    {
        \App\Models\User::factory()->create(['username' => 'taken']);

        $component = Volt::test('pages.auth.register')
            ->set('name', 'Test User')
            ->set('username', 'taken')
            ->set('email', 'test2@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password');

        $component->call('register');

        $component->assertHasErrors(['username']);
    }

    #[DataProvider('reservedUsernameProvider')]
    public function test_registration_rejects_reserved_usernames(string $reserved): void
    {
        $component = Volt::test('pages.auth.register')
            ->set('name', 'Test User')
            ->set('username', $reserved)
            ->set('email', 'reserved-test@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password');

        $component->call('register');

        $component->assertHasErrors(['username']);
        $this->assertGuest();
    }

    public static function reservedUsernameProvider(): array
    {
        return [
            ['login'],
            ['dashboard'],
            ['links'],
            ['admin'],
            ['LOGIN'],
        ];
    }
}
