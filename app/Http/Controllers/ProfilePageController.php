<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class ProfilePageController extends Controller
{
    public function show(string $username): View
    {
        $user = User::where('username', $username)->with(['links', 'socialLinks'])->firstOrFail();

        return view('profile-page', ['user' => $user]);
    }
}
