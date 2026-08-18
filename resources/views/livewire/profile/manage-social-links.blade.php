<?php

use App\Support\SocialPlatform;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public string $url = '';

    protected function rules(): array
    {
        return [
            'url' => ['required', 'url:http,https', 'max:2048'],
        ];
    }

    public function addSocialLink(): void
    {
        $validated = $this->validate();

        Auth::user()->socialLinks()->create([
            ...$validated,
            'position' => (Auth::user()->socialLinks()->max('position') ?? 0) + 1,
        ]);

        $this->reset('url');
    }

    public function deleteSocialLink(int $id): void
    {
        Auth::user()->socialLinks()->findOrFail($id)->delete();
    }

    public function platformLabel(string $url): string
    {
        return SocialPlatform::detect($url)['label'];
    }

    public function platformKey(string $url): string
    {
        return SocialPlatform::detect($url)['key'];
    }
}; ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">{{ __('社群連結') }}</h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ __('貼上你的社群主頁網址，系統會自動辨識並顯示對應的圖示，顯示在公開頁面頭像下方。') }}
        </p>
    </header>

    @php $count = auth()->user()->socialLinks->count(); @endphp

    <form wire:submit="addSocialLink" class="mt-6 flex items-start gap-4">
        <div class="flex-1">
            <x-input-label for="social-url" :value="__('網址')" class="sr-only" />
            <x-text-input wire:model="url" id="social-url" type="text" class="block w-full" placeholder="https://twitter.com/yourname" />
            <x-input-error class="mt-2" :messages="$errors->get('url')" />
        </div>

        @if ($count >= 8)
            <x-primary-button type="submit" wire:confirm="你已經有 {{ $count }} 個社群連結了，太多可能會讓版面不好看，確定要繼續新增嗎？">
                {{ __('新增') }}
            </x-primary-button>
        @else
            <x-primary-button type="submit">
                {{ __('新增') }}
            </x-primary-button>
        @endif
    </form>

    <div class="mt-6 divide-y">
        @forelse (auth()->user()->socialLinks as $social)
            <div class="py-3 flex items-center justify-between gap-4" wire:key="social-{{ $social->id }}">
                <div class="flex items-center gap-3 min-w-0">
                    <x-social-icon :platform="$social->platform" class="w-6 h-6 text-gray-700 shrink-0" />
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-900">{{ $social->platform_label }}</p>
                        <p class="text-sm text-gray-500 truncate">{{ $social->url }}</p>
                    </div>
                </div>

                <button wire:click="deleteSocialLink({{ $social->id }})" wire:confirm="{{ __('確定要刪除這個社群連結嗎？') }}" class="text-sm text-red-600 hover:text-red-800 shrink-0">
                    {{ __('刪除') }}
                </button>
            </div>
        @empty
            <p class="py-3 text-sm text-gray-500">{{ __('還沒有加任何社群連結。') }}</p>
        @endforelse
    </div>
</section>
