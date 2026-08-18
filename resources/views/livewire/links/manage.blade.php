<?php

use App\Models\Link;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component
{
    public string $title = '';
    public string $url = '';
    public string $description = '';
    public ?int $editingId = null;

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url:http,https', 'max:2048'],
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function addLink(): void
    {
        $throttleKey = 'add-link|'.Auth::id();

        if (RateLimiter::tooManyAttempts($throttleKey, 10)) {
            throw ValidationException::withMessages([
                'title' => __('新增次數過多，請於 :seconds 秒後再試。', ['seconds' => RateLimiter::availableIn($throttleKey)]),
            ]);
        }

        RateLimiter::hit($throttleKey);

        $validated = $this->validate();

        Auth::user()->links()->create([
            ...$validated,
            'position' => (Auth::user()->links()->max('position') ?? 0) + 1,
        ]);

        $this->reset(['title', 'url', 'description']);
    }

    public function edit(int $id): void
    {
        $link = Auth::user()->links()->findOrFail($id);

        $this->editingId = $link->id;
        $this->title = $link->title;
        $this->url = $link->url;
        $this->description = $link->description ?? '';
    }

    public function updateLink(): void
    {
        $validated = $this->validate();

        $link = Auth::user()->links()->findOrFail($this->editingId);
        $link->update($validated);

        $this->cancelEdit();
    }

    public function cancelEdit(): void
    {
        $this->reset(['title', 'url', 'description', 'editingId']);
        $this->resetValidation();
    }

    public function deleteLink(int $id): void
    {
        Auth::user()->links()->findOrFail($id)->delete();

        if ($this->editingId === $id) {
            $this->cancelEdit();
        }
    }

    public function moveUp(int $id): void
    {
        $this->swapWithNeighbor($id, 'previous');
    }

    public function moveDown(int $id): void
    {
        $this->swapWithNeighbor($id, 'next');
    }

    protected function swapWithNeighbor(int $id, string $direction): void
    {
        $links = Auth::user()->links;
        $index = $links->search(fn (Link $link) => $link->id === $id);

        if ($index === false) {
            return;
        }

        $neighborIndex = $direction === 'previous' ? $index - 1 : $index + 1;

        if (! isset($links[$neighborIndex])) {
            return;
        }

        $current = $links[$index];
        $neighbor = $links[$neighborIndex];

        [$current->position, $neighbor->position] = [$neighbor->position, $current->position];

        $current->save();
        $neighbor->save();
    }
}; ?>

<div class="space-y-6">
    <div class="bg-white p-6 shadow sm:rounded-lg">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            {{ $editingId ? __('編輯連結') : __('新增連結') }}
        </h3>

        <form wire:submit="{{ $editingId ? 'updateLink' : 'addLink' }}" class="space-y-4">
            <div>
                <x-input-label for="title" :value="__('標題')" />
                <x-text-input wire:model="title" id="title" type="text" class="mt-1 block w-full" />
                <x-input-error class="mt-2" :messages="$errors->get('title')" />
            </div>

            <div>
                <x-input-label for="url" :value="__('網址')" />
                <x-text-input wire:model="url" id="url" type="text" class="mt-1 block w-full" placeholder="https://" />
                <x-input-error class="mt-2" :messages="$errors->get('url')" />
            </div>

            <div>
                <x-input-label for="description" :value="__('描述（選填）')" />
                <x-text-input wire:model="description" id="description" type="text" class="mt-1 block w-full" />
                <x-input-error class="mt-2" :messages="$errors->get('description')" />
            </div>

            <div class="flex items-center gap-4">
                <x-primary-button type="submit">
                    {{ $editingId ? __('儲存') : __('新增') }}
                </x-primary-button>

                @if ($editingId)
                    <x-secondary-button type="button" wire:click="cancelEdit">
                        {{ __('取消') }}
                    </x-secondary-button>
                @endif
            </div>
        </form>
    </div>

    <div class="bg-white shadow sm:rounded-lg divide-y">
        @forelse (auth()->user()->links as $link)
            <div class="p-4 flex items-center justify-between gap-4" wire:key="link-{{ $link->id }}">
                <div class="min-w-0">
                    <p class="font-medium text-gray-900 truncate">{{ $link->title }}</p>
                    <a href="{{ $link->url }}" target="_blank" rel="noopener" class="text-sm text-indigo-600 truncate block">{{ $link->url }}</a>
                    @if ($link->description)
                        <p class="text-sm text-gray-500 truncate">{{ $link->description }}</p>
                    @endif
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <button wire:click="moveUp({{ $link->id }})" class="text-gray-400 hover:text-gray-700" title="{{ __('上移') }}">&uarr;</button>
                    <button wire:click="moveDown({{ $link->id }})" class="text-gray-400 hover:text-gray-700" title="{{ __('下移') }}">&darr;</button>
                    <button wire:click="edit({{ $link->id }})" class="text-sm text-indigo-600 hover:text-indigo-800">{{ __('編輯') }}</button>
                    <button wire:click="deleteLink({{ $link->id }})" wire:confirm="{{ __('確定要刪除這個連結嗎？') }}" class="text-sm text-red-600 hover:text-red-800">{{ __('刪除') }}</button>
                </div>
            </div>
        @empty
            <p class="p-4 text-sm text-gray-500">{{ __('還沒有任何連結，用上方表單新增第一個吧。') }}</p>
        @endforelse
    </div>
</div>
