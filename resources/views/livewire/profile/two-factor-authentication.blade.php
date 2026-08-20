<?php

use App\Support\TwoFactorAuthenticationProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Volt\Component;

new class extends Component
{
    public string $code = '';

    public string $current_password = '';

    /** @var array<int, string>|null */
    public ?array $recoveryCodesToShow = null;

    protected function provider(): TwoFactorAuthenticationProvider
    {
        return app(TwoFactorAuthenticationProvider::class);
    }

    public function getIsConfirmingProperty(): bool
    {
        $user = Auth::user();

        return ! $user->hasEnabledTwoFactorAuthentication() && ! is_null($user->two_factor_secret);
    }

    public function getQrCodeSvgProperty(): ?string
    {
        $user = Auth::user();

        if (! $this->isConfirming) {
            return null;
        }

        return $this->provider()->qrCodeSvg($user->email, $user->two_factor_secret);
    }

    public function enableTwoFactorAuthentication(): void
    {
        $user = Auth::user();

        $user->forceFill([
            'two_factor_secret' => $this->provider()->generateSecretKey(),
            'two_factor_recovery_codes' => $this->provider()->generateRecoveryCodes(),
            'two_factor_confirmed_at' => null,
        ])->save();
    }

    public function cancelTwoFactorAuthentication(): void
    {
        $user = Auth::user();

        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        $this->reset('code');
    }

    public function confirmTwoFactorAuthentication(): void
    {
        $this->validate(['code' => ['required', 'string']]);

        $user = Auth::user();

        if (! $this->provider()->verify($user->two_factor_secret, $this->code)) {
            $this->addError('code', __('驗證碼不正確。'));

            return;
        }

        $user->forceFill(['two_factor_confirmed_at' => now()])->save();

        $this->recoveryCodesToShow = $user->two_factor_recovery_codes;

        $this->reset('code');
    }

    public function regenerateRecoveryCodes(): void
    {
        $this->validate(['current_password' => ['required', 'string', 'current_password']]);

        $user = Auth::user();

        $codes = $this->provider()->generateRecoveryCodes();

        $user->forceFill(['two_factor_recovery_codes' => $codes])->save();

        $this->recoveryCodesToShow = $codes;

        $this->reset('current_password');
    }

    public function disableTwoFactorAuthentication(): void
    {
        $user = Auth::user();

        abort_if($user->is_admin, 403, __('管理員帳號必須啟用雙重驗證，無法停用。'));

        $this->validate(['current_password' => ['required', 'string', 'current_password']]);

        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        $this->reset('current_password');
        $this->recoveryCodesToShow = null;
    }
}; ?>

<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('雙重驗證') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('為你的帳號加上一層額外的登入保護，登入時除了密碼外還需要輸入驗證器 App 產生的驗證碼。') }}
        </p>
    </header>

    @if (auth()->user()->is_admin && ! auth()->user()->hasEnabledTwoFactorAuthentication())
        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-md text-sm text-yellow-800">
            {{ __('管理員帳號必須啟用雙重驗證，請先完成以下設定。') }}
        </div>
    @endif

    @if (auth()->user()->hasEnabledTwoFactorAuthentication())
        <div class="p-4 bg-green-50 border border-green-200 rounded-md text-sm text-green-800">
            {{ __('雙重驗證已啟用。') }}
        </div>

        @if ($recoveryCodesToShow)
            <div class="p-4 bg-gray-50 border border-gray-200 rounded-md">
                <p class="text-sm text-gray-700 mb-2">
                    {{ __('請妥善保存以下備用碼，每組只能使用一次，遺失驗證器 App 時可用來登入。') }}
                </p>
                <ul class="font-mono text-sm grid grid-cols-2 gap-1">
                    @foreach ($recoveryCodesToShow as $recoveryCode)
                        <li>{{ $recoveryCode }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form wire:submit="regenerateRecoveryCodes" class="flex items-start gap-4">
            <div class="flex-1">
                <x-input-label for="two_factor_current_password_regenerate" :value="__('目前密碼')" />
                <x-text-input wire:model="current_password" id="two_factor_current_password_regenerate" type="password" class="mt-1 block w-full" autocomplete="current-password" />
                <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
            </div>
            <x-secondary-button class="mt-6">
                {{ __('重新產生備用碼') }}
            </x-secondary-button>
        </form>

        @unless (auth()->user()->is_admin)
            <form wire:submit="disableTwoFactorAuthentication" class="flex items-start gap-4">
                <div class="flex-1">
                    <x-input-label for="two_factor_current_password_disable" :value="__('目前密碼')" />
                    <x-text-input wire:model="current_password" id="two_factor_current_password_disable" type="password" class="mt-1 block w-full" autocomplete="current-password" />
                </div>
                <x-danger-button class="mt-6">
                    {{ __('停用雙重驗證') }}
                </x-danger-button>
            </form>
        @endunless
    @elseif ($this->isConfirming)
        <div class="space-y-4">
            <div class="[&>svg]:w-48 [&>svg]:h-48">
                {!! $this->qrCodeSvg !!}
            </div>

            <p class="text-sm text-gray-600">
                {{ __('請用驗證器 App 掃描上方 QR Code，或手動輸入以下密鑰：') }}
                <code class="block mt-1 font-mono text-sm">{{ auth()->user()->two_factor_secret }}</code>
            </p>

            <form wire:submit="confirmTwoFactorAuthentication" class="flex items-start gap-4">
                <div class="flex-1">
                    <x-input-label for="code" :value="__('驗證碼')" />
                    <x-text-input wire:model="code" id="code" type="text" inputmode="numeric" class="mt-1 block w-full" autofocus autocomplete="one-time-code" />
                    <x-input-error :messages="$errors->get('code')" class="mt-2" />
                </div>
                <x-primary-button class="mt-6">
                    {{ __('確認並啟用') }}
                </x-primary-button>
            </form>

            <button type="button" wire:click="cancelTwoFactorAuthentication" class="underline text-sm text-gray-600 hover:text-gray-900">
                {{ __('取消設定') }}
            </button>
        </div>
    @else
        <x-primary-button wire:click="enableTwoFactorAuthentication">
            {{ __('啟用雙重驗證') }}
        </x-primary-button>
    @endif
</section>
