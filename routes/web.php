<?php

use App\Http\Controllers\ProfilePageController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::view('links', 'links')
    ->middleware(['auth'])
    ->name('links.index');

Route::view('admin', 'admin.dashboard')
    ->middleware(['auth', 'verified', 'admin'])
    ->name('admin.dashboard');

require __DIR__.'/auth.php';

// 公開個人頁面，須放在所有靜態路由之後，避免蓋掉 /login、/dashboard 等既有路徑
Route::get('/{username}', [ProfilePageController::class, 'show'])
    ->where('username', '[A-Za-z0-9_-]+')
    ->name('profile.show');
