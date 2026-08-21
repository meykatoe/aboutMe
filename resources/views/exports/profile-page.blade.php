<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $user->name }} ({{ '@'.$user->username }}) - {{ config('app.name') }} 匯出資料</title>
        <style>
            * { box-sizing: border-box; }
            body {
                margin: 0;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
                background: #f3f4f6;
                color: #111827;
            }
            .page {
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                align-items: center;
                padding: 64px 24px 32px;
            }
            .avatar, .avatar-fallback {
                width: 96px;
                height: 96px;
                border-radius: 9999px;
                object-fit: cover;
            }
            .avatar-fallback {
                display: flex;
                align-items: center;
                justify-content: center;
                background: #4f46e5;
                color: #fff;
                font-size: 1.875rem;
                font-weight: 600;
            }
            .name { margin: 16px 0 0; font-size: 1.5rem; font-weight: 600; }
            .username { margin: 0; color: #6b7280; }
            .bio {
                margin: 16px 0 0;
                color: #374151;
                text-align: center;
                max-width: 28rem;
                white-space: pre-line;
            }
            .bio-empty { margin: 16px 0 0; color: #9ca3af; font-size: 0.875rem; }
            .social-icons {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                gap: 12px;
                margin-top: 16px;
                max-width: 28rem;
            }
            .social-icon {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 44px;
                height: 44px;
                border-radius: 9999px;
                background: #fff;
                border: 1px solid #e5e7eb;
                color: #374151;
                text-decoration: none;
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            }
            .social-icon svg { width: 20px; height: 20px; }
            .links {
                width: 100%;
                max-width: 28rem;
                margin: 32px 0 24px;
                display: flex;
                flex-direction: column;
                gap: 12px;
            }
            .link-card {
                display: block;
                background: #fff;
                border: 1px solid #e5e7eb;
                border-radius: 0.5rem;
                padding: 16px;
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
                text-decoration: none;
                color: inherit;
            }
            .link-title { margin: 0; font-weight: 500; color: #111827; }
            .link-desc { margin: 4px 0 0; font-size: 0.875rem; color: #6b7280; }
            .footer {
                margin-top: auto;
                padding-top: 24px;
                font-size: 0.75rem;
                color: #9ca3af;
                text-align: center;
            }
            @if ($backgroundImagePcDataUri || $backgroundImageMobileDataUri)
                .page {
                    background-size: cover;
                    background-position: center;
                    background-repeat: no-repeat;
                    @if ($user->background_color) background-color: {{ $user->background_color }}; @endif
                    background-image: url('{{ $backgroundImageMobileDataUri ?? $backgroundImagePcDataUri }}');
                }
                @media (min-width: 768px) {
                    .page {
                        background-image: url('{{ $backgroundImagePcDataUri ?? $backgroundImageMobileDataUri }}');
                    }
                }
            @elseif ($user->background_color)
                .page { background-color: {{ $user->background_color }}; }
            @endif
        </style>
    </head>
    <body>
        <div class="page">
            @if ($avatarDataUri)
                <img src="{{ $avatarDataUri }}" class="avatar" alt="{{ $user->name }}">
            @else
                <div class="avatar-fallback">
                    {{ Str::of($user->name)->explode(' ')->map(fn ($part) => Str::substr($part, 0, 1))->take(2)->implode('') }}
                </div>
            @endif

            <h1 class="name">{{ $user->name }}</h1>
            <p class="username">{{ '@'.$user->username }}</p>

            @if ($user->bio)
                <p class="bio">{{ $user->bio }}</p>
            @else
                <p class="bio-empty">這位使用者還沒有設定個人介紹。</p>
            @endif

            @if ($user->socialLinks->isNotEmpty())
                <div class="social-icons">
                    @foreach ($user->socialLinks as $social)
                        <a href="{{ $social->url }}" target="_blank" rel="noopener" title="{{ $social->platform_label }}" class="social-icon">
                            <x-social-icon :platform="$social->platform" />
                        </a>
                    @endforeach
                </div>
            @endif

            @if ($user->links->isNotEmpty())
                <div class="links">
                    @foreach ($user->links as $link)
                        <a href="{{ $link->url }}" target="_blank" rel="noopener" class="link-card">
                            <p class="link-title">{{ $link->title }}</p>
                            @if ($link->description)
                                <p class="link-desc">{{ $link->description }}</p>
                            @endif
                        </a>
                    @endforeach
                </div>
            @endif

            <p class="footer">
                由 {{ config('app.name') }} 於 {{ $exportedAt->format('Y-m-d H:i') }} 匯出，可離線開啟本檔案。
            </p>
        </div>
    </body>
</html>
