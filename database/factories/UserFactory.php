<?php

namespace Database\Factories;

use App\Models\User;
use App\Support\TwoFactorAuthenticationProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'username' => fake()->unique()->regexify('[a-z][a-z0-9_]{5,14}'),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'is_active' => true,
            'is_public' => true,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user is an administrator.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_admin' => true,
        ]);
    }

    /**
     * Indicate that the user has confirmed two-factor authentication.
     */
    public function withTwoFactorEnabled(): static
    {
        $provider = app(TwoFactorAuthenticationProvider::class);

        return $this->state(fn (array $attributes) => [
            'two_factor_secret' => $provider->generateSecretKey(),
            'two_factor_recovery_codes' => $provider->generateRecoveryCodes(),
            'two_factor_confirmed_at' => now(),
        ]);
    }
}
