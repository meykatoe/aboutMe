<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>

            @php
                $profileUrl = route('profile.show', ['username' => auth()->user()->username]);
            @endphp
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900" x-data="{ copied: false }">
                    <h3 class="font-semibold text-lg mb-4">{{ __('分享你的個人頁面') }}</h3>

                    <div class="flex flex-col sm:flex-row gap-6">
                        <canvas data-qrcode-url="{{ $profileUrl }}" class="shrink-0"></canvas>

                        <div class="flex-1 min-w-0">
                            <label for="profile-url" class="block text-sm font-medium text-gray-700 mb-1">{{ __('你的公開網址') }}</label>
                            <div class="flex gap-2">
                                <input id="profile-url" type="text" readonly value="{{ $profileUrl }}"
                                       class="flex-1 min-w-0 border-gray-300 rounded-md shadow-sm text-sm text-gray-600 bg-gray-50 focus:ring-indigo-500 focus:border-indigo-500"
                                       onclick="this.select()">
                                <x-secondary-button
                                    x-on:click="
                                        navigator.clipboard.writeText('{{ $profileUrl }}');
                                        copied = true;
                                        setTimeout(() => copied = false, 2000);
                                    "
                                >
                                    <span x-show="!copied">{{ __('複製連結') }}</span>
                                    <span x-show="copied">{{ __('已複製！') }}</span>
                                </x-secondary-button>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">{{ __('分享此連結或掃描 QR code，讓其他人快速造訪你的個人頁面。') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
