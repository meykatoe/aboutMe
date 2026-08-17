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
            @if ($user->avatar_url)
                <img src="{{ $user->avatar_url }}" class="w-24 h-24 rounded-full object-cover">
            @else
                <div class="flex items-center justify-center w-24 h-24 rounded-full bg-indigo-600 text-white text-3xl font-semibold">
                    {{ Str::of($user->name)->explode(' ')->map(fn ($part) => Str::substr($part, 0, 1))->take(2)->implode('') }}
                </div>
            @endif

            @if ($user->socialLinks->isNotEmpty())
                <div class="grid grid-cols-4 gap-3 mt-4">
                    @foreach ($user->socialLinks as $social)
                        <a href="{{ $social->url }}" target="_blank" rel="noopener" title="{{ $social->platform_label }}"
                           class="flex items-center justify-center w-11 h-11 rounded-full bg-white border border-gray-200 text-gray-700 shadow-sm hover:shadow-md hover:border-indigo-300 hover:text-indigo-600 transition">
                            <x-social-icon :platform="$social->platform" class="w-5 h-5" />
                        </a>
                    @endforeach
                </div>
            @endif

            <h1 class="mt-4 text-2xl font-semibold">{{ $user->name }}</h1>
            <p class="text-gray-500">{{ '@'.$user->username }}</p>

            @if ($user->bio)
                <p class="mt-4 text-gray-700 text-center max-w-md whitespace-pre-line">{{ $user->bio }}</p>
            @else
                <p class="mt-4 text-gray-400 text-sm">這位使用者還沒有設定個人介紹。</p>
            @endif

            @if ($user->links->isNotEmpty())
                <div class="w-full max-w-md mt-8 mb-16 space-y-3">
                    @foreach ($user->links as $link)
                        <a href="{{ $link->url }}" target="_blank" rel="noopener"
                           class="block bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md hover:border-indigo-300 transition">
                            <p class="font-medium text-gray-900">{{ $link->title }}</p>
                            @if ($link->description)
                                <p class="text-sm text-gray-500">{{ $link->description }}</p>
                            @endif
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </body>
</html>
