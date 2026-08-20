<?php

use App\Models\User;
use App\Support\TwoFactorAuthenticationProvider;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $code = '';

    public string $recovery_code = '';

    public bool $useRecoveryCode = false;

    public function mount(): void
    {
        if (! session()->has('login.id')) {
            $this->redirect(route('login', absolute: false), navigate: true);
        }
    }

    public function toggleRecoveryCode(): void
    {
        $this->useRecoveryCode = ! $this->useRecoveryCode;
        $this->reset('code', 'recovery_code');
        $this->resetErrorBag();
    }

    /**
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $user = $this->challengedUser();

        if ($this->useRecoveryCode) {
            $this->validate(['recovery_code' => ['required', 'string']]);
            $this->attemptRecoveryCode($user);
        } else {
            $this->validate(['code' => ['required', 'string']]);
            $this->attemptCode($user);
        }

        RateLimiter::clear($this->throttleKey());

        Auth::login($user, session('login.remember', false));

        session()->forget(['login.id', 'login.remember']);

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    /**
     * @throws ValidationException
     */
    protected function attemptCode(User $user): void
    {
        $provider = app(TwoFactorAuthenticationProvider::class);

        if (! $provider->verify($user->two_factor_secret, $this->code)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'code' => __('驗證碼不正確。'),
            ]);
        }
    }

    /**
     * @throws ValidationException
     */
    protected function attemptRecoveryCode(User $user): void
    {
        $codes = $user->two_factor_recovery_codes ?? [];
        $index = collect($codes)->search(fn ($stored) => hash_equals($stored, $this->recovery_code));

        if ($index === false) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'recovery_code' => __('備用碼不正確。'),
            ]);
        }

        $codes[$index] = Str::upper(Str::random(4).'-'.Str::random(4).'-'.Str::random(4));

        $user->forceFill(['two_factor_recovery_codes' => $codes])->save();
    }

    /**
     * @throws ValidationException
     */
    protected function challengedUser(): User
    {
        $id = rescue(fn () => decrypt(session('login.id')), false, false);

        $user = $id ? User::find($id) : null;

        if (! $user || ! $user->is_active || ! $user->hasEnabledTwoFactorAuthentication()) {
            session()->forget(['login.id', 'login.remember']);

            throw ValidationException::withMessages([
                'code' => __('驗證階段已逾期，請重新登入。'),
            ]);
        }

        return $user;
    }

    /**
     * @throws ValidationException
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'code' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower((string) session('login.id')).'|'.request()->ip());
    }
}; ?>

<div>
    <div class="mb-4 text-sm text-gray-600">
        @if ($useRecoveryCode)
            {{ __('請輸入其中一組備用碼以完成登入。') }}
        @else
            {{ __('請開啟驗證器 App，輸入目前顯示的 6 位數驗證碼。') }}
        @endif
    </div>

    <form wire:submit="authenticate">
        @if ($useRecoveryCode)
            <div>
                <x-input-label for="recovery_code" :value="__('備用碼')" />
                <x-text-input wire:model="recovery_code" id="recovery_code" class="block mt-1 w-full" type="text" name="recovery_code" autofocus autocomplete="one-time-code" />
                <x-input-error :messages="$errors->get('recovery_code')" class="mt-2" />
            </div>
        @else
            <div>
                <x-input-label for="code" :value="__('驗證碼')" />
                <x-text-input wire:model="code" id="code" class="block mt-1 w-full" type="text" inputmode="numeric" name="code" autofocus autocomplete="one-time-code" />
                <x-input-error :messages="$errors->get('code')" class="mt-2" />
            </div>
        @endif

        <div class="flex items-center justify-between mt-4">
            <button type="button" wire:click="toggleRecoveryCode" class="underline text-sm text-gray-600 hover:text-gray-900">
                @if ($useRecoveryCode)
                    {{ __('改用驗證碼登入') }}
                @else
                    {{ __('改用備用碼登入') }}
                @endif
            </button>

            <x-primary-button>
                {{ __('登入') }}
            </x-primary-button>
        </div>
    </form>
</div>
