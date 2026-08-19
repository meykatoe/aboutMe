<?php

use App\Models\User;
use App\Support\AvatarProcessor;
use App\Models\UsernameHistory;
use App\Rules\ReservedUsername;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    public string $name = '';
    public string $username = '';
    public string $email = '';
    public string $bio = '';
    public bool $is_public = true;
    public $avatar = null;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->username = Auth::user()->username;
        $this->email = Auth::user()->email;
        $this->bio = Auth::user()->bio ?? '';
        $this->is_public = Auth::user()->is_public;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required', 'string', 'min:3', 'max:20', 'alpha_dash',
                Rule::unique(User::class)->ignore($user->id),
                new ReservedUsername,
            ],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'bio' => ['nullable', 'string', 'max:1000'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'],
            'is_public' => ['required', 'boolean'],
        ]);

        $user->fill([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'bio' => $validated['bio'],
        ]);

        $user->is_public = $validated['is_public'];

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($this->avatar) {
            try {
                $contents = AvatarProcessor::process($this->avatar);
            } catch (\RuntimeException) {
                throw ValidationException::withMessages([
                    'avatar' => __('無法讀取這個圖片檔案，請換一張再試。'),
                ]);
            }

            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }

            $path = 'avatars/'.Str::uuid().'.jpg';
            Storage::disk('public')->put($path, $contents);

            $user->avatar_path = $path;
            $this->avatar = null;
        }

        if ($user->isDirty('username')) {
            $oldUsername = $user->getOriginal('username');

            // 舊使用者名稱可能曾指向別人（已被回收），改由這次變更接手；
            // 新使用者名稱若曾是某筆歷史紀錄，該紀錄現已失效，需一併清除。
            UsernameHistory::where('username', $oldUsername)->delete();
            UsernameHistory::where('username', $user->username)->delete();

            $user->usernameHistories()->create(['username' => $oldUsername]);
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form wire:submit="updateProfileInformation" class="mt-6 space-y-6">
        <div>
            <x-input-label for="avatar" :value="__('Avatar')" />

            <div class="mt-1 flex items-center gap-4">
                @if ($avatar)
                    <img src="{{ $avatar->temporaryUrl() }}" class="w-16 h-16 rounded-full object-cover">
                @elseif (auth()->user()->avatar_url)
                    <img src="{{ auth()->user()->avatar_url }}" class="w-16 h-16 rounded-full object-cover">
                @else
                    <div class="w-16 h-16 rounded-full bg-indigo-600 text-white flex items-center justify-center text-xl font-semibold">
                        {{ Str::of(auth()->user()->name)->explode(' ')->map(fn ($part) => Str::substr($part, 0, 1))->take(2)->implode('') }}
                    </div>
                @endif

                <input type="file" wire:model="avatar" id="avatar" accept="image/*">
            </div>

            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
        </div>

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input wire:model="name" id="name" name="name" type="text" class="mt-1 block w-full" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="username" :value="__('Username')" />
            <x-text-input wire:model="username" id="username" name="username" type="text" class="mt-1 block w-full" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('username')" />
            <p class="mt-1 text-sm text-gray-500">
                {{ __('Your public page URL:') }} {{ url('/'.$username) }}
            </p>
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" name="email" type="email" class="mt-1 block w-full" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button wire:click.prevent="sendVerification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <x-input-label for="bio" :value="__('Bio')" />
            <textarea wire:model="bio" id="bio" name="bio" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" maxlength="1000"></textarea>
            <x-input-error class="mt-2" :messages="$errors->get('bio')" />
        </div>

        <div class="flex items-center gap-2">
            <input wire:model="is_public" id="is_public" name="is_public" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
            <x-input-label for="is_public" :value="__('Make my page public')" />
            <x-input-error class="mt-2" :messages="$errors->get('is_public')" />
        </div>
        <p class="text-sm text-gray-500">
            {{ __('When turned off, only you can view your page while logged in — everyone else will see a 404.') }}
        </p>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            <x-action-message class="me-3" on="profile-updated">
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </form>
</section>
