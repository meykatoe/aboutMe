<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UsernameHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProfilePageController extends Controller
{
    public function show(string $username): View|RedirectResponse
    {
        $user = User::where('username', $username)->with(['links', 'socialLinks'])->first();

        if ($user) {
            $user->increment('profile_views');

            return view('profile-page', ['user' => $user]);
        }

        $history = UsernameHistory::where('username', $username)->first();

        abort_unless($history, 404);

        return redirect()->route('profile.show', ['username' => $history->user->username], 301);
    }
}
