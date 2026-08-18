<?php

use App\Models\User;
use App\Rules\ReservedUsername;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public string $search = '';

    public ?int $editingId = null;

    public string $name = '';

    public string $username = '';

    public string $email = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function users()
    {
        return User::query()
            ->when($this->search, function ($query) {
                $query->where(function ($query) {
                    $query->where('name', 'like', "%{$this->search}%")
                        ->orWhere('username', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('created_at')
            ->paginate(15);
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required', 'string', 'min:3', 'max:20', 'alpha_dash',
                Rule::unique(User::class)->ignore($this->editingId),
                new ReservedUsername,
            ],
            'email' => [
                'required', 'string', 'lowercase', 'email', 'max:255',
                Rule::unique(User::class)->ignore($this->editingId),
            ],
        ];
    }

    public function edit(int $id): void
    {
        $user = User::findOrFail($id);

        $this->editingId = $user->id;
        $this->name = $user->name;
        $this->username = $user->username;
        $this->email = $user->email;
    }

    public function updateUser(): void
    {
        $validated = $this->validate();

        User::findOrFail($this->editingId)->update($validated);

        $this->cancelEdit();
    }

    public function cancelEdit(): void
    {
        $this->reset(['editingId', 'name', 'username', 'email']);
        $this->resetValidation();
    }

    public function toggleAdmin(int $id): void
    {
        if ($id === Auth::id()) {
            return;
        }

        $user = User::findOrFail($id);
        $user->forceFill(['is_admin' => ! $user->is_admin])->save();
    }

    public function toggleActive(int $id): void
    {
        if ($id === Auth::id()) {
            return;
        }

        $user = User::findOrFail($id);
        $user->forceFill(['is_active' => ! $user->is_active])->save();
    }

    public function deleteUser(int $id): void
    {
        if ($id === Auth::id()) {
            return;
        }

        User::findOrFail($id)->delete();

        if ($this->editingId === $id) {
            $this->cancelEdit();
        }
    }
}; ?>

<div class="space-y-6">
    @if ($editingId)
        <div class="bg-white p-6 shadow sm:rounded-lg">
            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('編輯帳號') }}</h3>

            <form wire:submit="updateUser" class="space-y-4">
                <div>
                    <x-input-label for="name" :value="__('姓名')" />
                    <x-text-input wire:model="name" id="name" type="text" class="mt-1 block w-full" />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <div>
                    <x-input-label for="username" :value="__('使用者名稱')" />
                    <x-text-input wire:model="username" id="username" type="text" class="mt-1 block w-full" />
                    <x-input-error class="mt-2" :messages="$errors->get('username')" />
                </div>

                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input wire:model="email" id="email" type="text" class="mt-1 block w-full" />
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                </div>

                <div class="flex items-center gap-4">
                    <x-primary-button type="submit">{{ __('儲存') }}</x-primary-button>
                    <x-secondary-button type="button" wire:click="cancelEdit">{{ __('取消') }}</x-secondary-button>
                </div>
            </form>
        </div>
    @endif

    <div class="bg-white p-6 shadow sm:rounded-lg">
        <x-text-input wire:model.live.debounce.300ms="search" type="text" class="block w-full" placeholder="{{ __('搜尋姓名、使用者名稱或 Email') }}" />
    </div>

    <div class="bg-white shadow sm:rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('帳號') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('角色') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('狀態') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('註冊時間') }}</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('操作') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($this->users as $user)
                    <tr wire:key="user-{{ $user->id }}">
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900">{{ $user->name }}</p>
                            <p class="text-sm text-gray-500">&commat;{{ $user->username }} &middot; {{ $user->email }}</p>
                        </td>
                        <td class="px-4 py-3">
                            @if ($user->is_admin)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">{{ __('管理員') }}</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">{{ __('一般使用者') }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if ($user->is_active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">{{ __('正常') }}</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">{{ __('已停權') }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $user->created_at->format('Y-m-d') }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-3">
                                <button wire:click="edit({{ $user->id }})" class="text-sm text-indigo-600 hover:text-indigo-800">{{ __('編輯') }}</button>

                                @if ($user->id !== auth()->id())
                                    <button
                                        wire:click="toggleAdmin({{ $user->id }})"
                                        wire:confirm="{{ $user->is_admin ? __('確定要將此帳號降為一般使用者嗎？') : __('確定要將此帳號設為管理員嗎？') }}"
                                        class="text-sm text-gray-600 hover:text-gray-900"
                                    >
                                        {{ $user->is_admin ? __('取消管理員') : __('設為管理員') }}
                                    </button>

                                    <button
                                        wire:click="toggleActive({{ $user->id }})"
                                        wire:confirm="{{ $user->is_active ? __('確定要停權此帳號嗎？該帳號將無法登入，但資料會保留。') : __('確定要恢復此帳號嗎？') }}"
                                        class="text-sm {{ $user->is_active ? 'text-amber-600 hover:text-amber-800' : 'text-green-600 hover:text-green-800' }}"
                                    >
                                        {{ $user->is_active ? __('停權') : __('恢復') }}
                                    </button>

                                    <button wire:click="deleteUser({{ $user->id }})" wire:confirm="{{ __('確定要刪除此帳號嗎？此操作無法復原。') }}" class="text-sm text-red-600 hover:text-red-800">
                                        {{ __('刪除') }}
                                    </button>
                                @else
                                    <span class="text-sm text-gray-400">{{ __('（目前登入帳號）') }}</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">{{ __('找不到符合條件的帳號。') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $this->users->links() }}
</div>
