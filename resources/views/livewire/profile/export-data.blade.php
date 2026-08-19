<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Component;

new class extends Component
{
    public function export()
    {
        $user = Auth::user()->load(['links', 'socialLinks']);

        $avatarDataUri = null;

        if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
            $mimeType = Storage::disk('public')->mimeType($user->avatar_path) ?: 'image/jpeg';
            $contents = Storage::disk('public')->get($user->avatar_path);

            $avatarDataUri = 'data:'.$mimeType.';base64,'.base64_encode($contents);
        }

        $html = view('exports.profile-page', [
            'user' => $user,
            'avatarDataUri' => $avatarDataUri,
            'exportedAt' => now(),
        ])->render();

        $filename = $user->username.'-profile-'.now()->format('Ymd-His').'.html';

        return response()->streamDownload(
            fn () => print($html),
            $filename,
            ['Content-Type' => 'text/html; charset=UTF-8']
        );
    }
}; ?>

<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            匯出個人頁面
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            將你的個人頁面（頭像、姓名、簡介與所有連結）匯出成一份 HTML 檔案，下載後可離線開啟，其中的連結與圖片皆可正常顯示與點擊，方便備份資料。
        </p>
    </header>

    <x-primary-button wire:click="export">匯出為 HTML</x-primary-button>
</section>
