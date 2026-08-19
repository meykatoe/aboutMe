<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UsernameHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfilePageController extends Controller
{
    public function show(string $username): View|RedirectResponse
    {
        $user = User::where('username', $username)->with(['links', 'socialLinks'])->first();

        if ($user) {
            abort_if(! $user->is_public && Auth::id() !== $user->id, 404);

            $user->increment('profile_views');

            return view('profile-page', ['user' => $user]);
        }

        $history = UsernameHistory::where('username', $username)->first();

        abort_unless($history, 404);

        return redirect()->route('profile.show', ['username' => $history->user->username], 301);
    }
}
