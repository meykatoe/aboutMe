<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $user->name }} ({{ '@'.$user->username }}) - {{ config('app.name') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col items-center pt-16 px-6 bg-gray-100">
            <div class="flex items-center justify-center w-24 h-24 rounded-full bg-indigo-600 text-white text-3xl font-semibold">
                {{ Str::of($user->name)->explode(' ')->map(fn ($part) => Str::substr($part, 0, 1))->take(2)->implode('') }}
            </div>

            <h1 class="mt-4 text-2xl font-semibold">{{ $user->name }}</h1>
            <p class="text-gray-500">{{ '@'.$user->username }}</p>

            @if ($user->bio)
                <p class="mt-4 text-gray-700 text-center max-w-md whitespace-pre-line">{{ $user->bio }}</p>
            @else
                <p class="mt-4 text-gray-400 text-sm">這位使用者還沒有設定個人介紹。</p>
            @endif
        </div>
    </body>
</html>
