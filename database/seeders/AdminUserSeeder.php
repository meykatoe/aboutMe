<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Creates the initial administrator account on first deploy. Safe to
     * run on every deploy — skips silently once an admin already exists.
     */
    public function run(): void
    {
        if (User::where('is_admin', true)->exists()) {
            return;
        }

        $password = Str::password(32);

        $admin = User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => config('app.admin_email') ?? 'admin@'.parse_url(config('app.url'), PHP_URL_HOST),
            'password' => $password,
            'email_verified_at' => now(),
        ]);

        $admin->forceFill(['is_admin' => true])->save();

        $this->command?->newLine();
        $this->command?->warn('已建立初始管理員帳號，請立即記下密碼（僅顯示這一次）：');
        $this->command?->line("  Username: {$admin->username}");
        $this->command?->line("  Email:    {$admin->email}");
        $this->command?->line("  Password: {$password}");
        $this->command?->newLine();
    }
}
