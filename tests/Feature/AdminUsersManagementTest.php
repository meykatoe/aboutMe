<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class AdminUsersManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_regular_user_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'email_verified_at' => now()]);

        $this->actingAs($admin)->get('/admin')->assertOk();
    }

    public function test_admin_can_search_users(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $match = User::factory()->create(['name' => 'Findme Person']);
        User::factory()->create(['name' => 'Someone Else']);

        Volt::actingAs($admin)
            ->test('admin.users-manage')
            ->set('search', 'Findme')
            ->assertSee($match->name)
            ->assertDontSee('Someone Else');
    }

    public function test_admin_can_edit_another_users_account(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $target = User::factory()->create(['name' => 'Old Name', 'username' => 'old_name_user']);

        Volt::actingAs($admin)
            ->test('admin.users-manage')
            ->call('edit', $target->id)
            ->set('name', 'New Name')
            ->call('updateUser')
            ->assertHasNoErrors();

        $this->assertSame('New Name', $target->fresh()->name);
    }

    public function test_admin_can_promote_and_demote_another_user(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $target = User::factory()->create(['is_admin' => false]);

        Volt::actingAs($admin)
            ->test('admin.users-manage')
            ->call('toggleAdmin', $target->id);

        $this->assertTrue($target->fresh()->is_admin);

        Volt::actingAs($admin)
            ->test('admin.users-manage')
            ->call('toggleAdmin', $target->id);

        $this->assertFalse($target->fresh()->is_admin);
    }

    public function test_admin_cannot_demote_themselves(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        Volt::actingAs($admin)
            ->test('admin.users-manage')
            ->call('toggleAdmin', $admin->id);

        $this->assertTrue($admin->fresh()->is_admin);
    }

    public function test_admin_can_delete_another_users_account(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $target = User::factory()->create();

        Volt::actingAs($admin)
            ->test('admin.users-manage')
            ->call('deleteUser', $target->id);

        $this->assertDatabaseMissing('users', ['id' => $target->id]);
    }

    public function test_admin_cannot_delete_themselves(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        Volt::actingAs($admin)
            ->test('admin.users-manage')
            ->call('deleteUser', $admin->id);

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }
}
