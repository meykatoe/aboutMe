<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'AboutMe') }} — 打造你的個人主頁</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600,700&display=swap" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased font-sans">
        <div class="bg-gray-50 text-black dark:bg-black dark:text-white">
            <div class="relative min-h-screen flex flex-col">
                <div class="relative w-full max-w-2xl mx-auto px-6 lg:max-w-6xl">
                    <header class="flex items-center justify-between py-8">
                        <a href="/" class="flex items-center gap-2 text-lg font-semibold text-black dark:text-white">
                            <span class="flex size-9 items-center justify-center rounded-full bg-[#FF2D20] text-white">A</span>
                            {{ config('app.name', 'AboutMe') }}
                        </a>

                        <livewire:welcome.navigation />
                    </header>

                    <main class="flex-1">
                        <section class="grid gap-10 py-12 lg:grid-cols-2 lg:items-center lg:py-20">
                            <div>
                                <h1 class="text-4xl font-bold leading-tight text-black dark:text-white sm:text-5xl">
                                    一頁搞定，<br />你的所有連結與自我介紹
                                </h1>

                                <p class="mt-6 text-base leading-relaxed text-black/60 dark:text-white/60">
                                    {{ config('app.name', 'AboutMe') }} 是開源、可自架的個人主頁產生器。免費註冊，幾分鐘內就能擁有一個專屬網址，集中展示你的簡介、頭像與所有社群連結。
                                </p>

                                <div class="mt-8 flex flex-wrap items-center gap-4">
                                    @if (Route::has('register'))
                                        <a
                                            href="{{ route('register') }}"
                                            class="rounded-md bg-[#FF2D20] px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#e0271c] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#FF2D20] focus-visible:ring-offset-2"
                                        >
                                            免費建立我的主頁
                                        </a>
                                    @endif

                                    <a
                                        href="{{ route('login') }}"
                                        class="rounded-md px-6 py-3 text-sm font-semibold text-black ring-1 ring-black/10 transition hover:ring-black/20 dark:text-white dark:ring-white/20 dark:hover:ring-white/40"
                                    >
                                        登入
                                    </a>
                                </div>
                            </div>

                            <div class="rounded-2xl bg-white p-8 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-zinc-800">
                                <div class="mx-auto flex max-w-xs flex-col items-center rounded-xl border border-black/5 bg-gray-50 p-8 text-center dark:border-white/10 dark:bg-black">
                                    <div class="flex size-20 items-center justify-center rounded-full bg-[#FF2D20]/10 text-2xl font-semibold text-[#FF2D20]">
                                        你
                                    </div>
                                    <p class="mt-4 text-lg font-semibold text-black dark:text-white">你的名字</p>
                                    <p class="text-sm text-black/50 dark:text-white/50">@{{ 'yourname' }}</p>
                                    <p class="mt-3 text-sm leading-relaxed text-black/60 dark:text-white/60">
                                        嗨，這裡是我的所有連結！歡迎逛逛 :)
                                    </p>
                                    <div class="mt-6 grid w-full gap-2">
                                        <span class="rounded-lg bg-white px-4 py-2 text-sm text-black shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:text-white dark:ring-white/10">我的部落格</span>
                                        <span class="rounded-lg bg-white px-4 py-2 text-sm text-black shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:text-white dark:ring-white/10">作品集</span>
                                        <span class="rounded-lg bg-white px-4 py-2 text-sm text-black shadow-sm ring-1 ring-black/5 dark:bg-zinc-900 dark:text-white dark:ring-white/10">聯絡我</span>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="grid gap-6 py-12 sm:grid-cols-3">
                            <div class="rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-zinc-800">
                                <h2 class="text-lg font-semibold text-black dark:text-white">專屬網址</h2>
                                <p class="mt-3 text-sm leading-relaxed text-black/60 dark:text-white/60">
                                    註冊後即可擁有 <code class="rounded bg-black/5 px-1 py-0.5 dark:bg-white/10">{{ request()->getHost() }}/你的名稱</code>，一個網址分享所有內容。
                                </p>
                            </div>

                            <div class="rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-zinc-800">
                                <h2 class="text-lg font-semibold text-black dark:text-white">自動辨識社群圖示</h2>
                                <p class="mt-3 text-sm leading-relaxed text-black/60 dark:text-white/60">
                                    貼上 Instagram、GitHub、YouTube 等連結，系統自動顯示對應的品牌圖示。
                                </p>
                            </div>

                            <div class="rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-zinc-800">
                                <h2 class="text-lg font-semibold text-black dark:text-white">開源可自架</h2>
                                <p class="mt-3 text-sm leading-relaxed text-black/60 dark:text-white/60">
                                    完整原始碼公開，任何人都能部署自己的版本，資料完全掌握在自己手上。
                                </p>
                            </div>
                        </section>
                    </main>

                    <footer class="py-16 text-center text-sm text-black/50 dark:text-white/50">
                        {{ config('app.name', 'AboutMe') }} &middot; 開源個人主頁產生器
                    </footer>
                </div>
            </div>
        </div>
    </body>
</html>
