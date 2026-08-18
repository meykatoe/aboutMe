<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Volt;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/profile');

        $response
            ->assertOk()
            ->assertSeeVolt('profile.update-profile-information-form')
            ->assertSeeVolt('profile.update-password-form')
            ->assertSeeVolt('profile.delete-user-form');
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $component = Volt::test('profile.update-profile-information-form')
            ->set('name', 'Test User')
            ->set('username', $user->username)
            ->set('email', 'test@example.com')
            ->set('bio', 'Hello, this is my bio.')
            ->call('updateProfileInformation');

        $component
            ->assertHasNoErrors()
            ->assertNoRedirect();

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertSame('Hello, this is my bio.', $user->bio);
        $this->assertNull($user->email_verified_at);
    }

    public function test_username_can_be_changed_and_old_username_redirects_to_new_one(): void
    {
        $user = User::factory()->create(['username' => 'old-name']);

        $this->actingAs($user);

        $component = Volt::test('profile.update-profile-information-form')
            ->set('name', $user->name)
            ->set('username', 'new-name')
            ->set('email', $user->email)
            ->call('updateProfileInformation');

        $component->assertHasNoErrors();

        $user->refresh();

        $this->assertSame('new-name', $user->username);
        $this->assertDatabaseHas('username_histories', [
            'user_id' => $user->id,
            'username' => 'old-name',
        ]);

        $this->get('/old-name')->assertRedirect('/new-name');
    }

    public function test_username_cannot_be_changed_to_an_already_taken_username(): void
    {
        User::factory()->create(['username' => 'taken']);
        $user = User::factory()->create(['username' => 'myname']);

        $this->actingAs($user);

        Volt::test('profile.update-profile-information-form')
            ->set('name', $user->name)
            ->set('username', 'taken')
            ->set('email', $user->email)
            ->call('updateProfileInformation')
            ->assertHasErrors('username');

        $this->assertSame('myname', $user->fresh()->username);
    }

    public function test_username_cannot_be_changed_to_a_reserved_word(): void
    {
        $user = User::factory()->create(['username' => 'myname']);

        $this->actingAs($user);

        Volt::test('profile.update-profile-information-form')
            ->set('name', $user->name)
            ->set('username', 'admin')
            ->set('email', $user->email)
            ->call('updateProfileInformation')
            ->assertHasErrors('username');

        $this->assertSame('myname', $user->fresh()->username);
    }

    public function test_reclaiming_an_old_username_invalidates_the_stale_redirect(): void
    {
        $userA = User::factory()->create(['username' => 'shared-name']);

        $this->actingAs($userA);

        Volt::test('profile.update-profile-information-form')
            ->set('name', $userA->name)
            ->set('username', 'renamed-a')
            ->set('email', $userA->email)
            ->call('updateProfileInformation')
            ->assertHasNoErrors();

        // shared-name 現在是歷史紀錄，指向 userA
        $this->get('/shared-name')->assertRedirect('/renamed-a');

        $userB = User::factory()->create(['username' => 'shared-name']);

        // 新使用者直接註冊了同名 username，歷史紀錄應已被清除，不再重導向
        $response = $this->get('/shared-name');
        $response->assertOk();
        $response->assertSee('@shared-name');
    }

    public function test_avatar_can_be_uploaded_and_replaces_the_previous_one(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $this->actingAs($user);

        Volt::test('profile.update-profile-information-form')
            ->set('name', $user->name)
            ->set('username', $user->username)
            ->set('email', $user->email)
            ->set('avatar', UploadedFile::fake()->image('avatar1.jpg'))
            ->call('updateProfileInformation')
            ->assertHasNoErrors();

        $user->refresh();
        $firstPath = $user->avatar_path;

        Storage::disk('public')->assertExists($firstPath);

        Volt::test('profile.update-profile-information-form')
            ->set('name', $user->name)
            ->set('username', $user->username)
            ->set('email', $user->email)
            ->set('avatar', UploadedFile::fake()->image('avatar2.jpg'))
            ->call('updateProfileInformation')
            ->assertHasNoErrors();

        $user->refresh();

        Storage::disk('public')->assertMissing($firstPath);
        Storage::disk('public')->assertExists($user->avatar_path);
        $this->assertNotSame($firstPath, $user->avatar_path);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $component = Volt::test('profile.update-profile-information-form')
            ->set('name', 'Test User')
            ->set('username', $user->username)
            ->set('email', $user->email)
            ->call('updateProfileInformation');

        $component
            ->assertHasNoErrors()
            ->assertNoRedirect();

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $component = Volt::test('profile.delete-user-form')
            ->set('password', 'password')
            ->call('deleteUser');

        $component
            ->assertHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $component = Volt::test('profile.delete-user-form')
            ->set('password', 'wrong-password')
            ->call('deleteUser');

        $component
            ->assertHasErrors('password')
            ->assertNoRedirect();

        $this->assertNotNull($user->fresh());
    }
}
