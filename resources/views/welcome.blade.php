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
        <div class="relative min-h-screen overflow-hidden bg-gray-100 text-black dark:bg-black dark:text-white">
            <!-- 背景裝飾：柔和灰階光暈，襯托玻璃質感 -->
            <div class="pointer-events-none absolute inset-0 -z-10">
                <div class="absolute -top-40 left-1/2 h-[36rem] w-[36rem] -translate-x-1/2 rounded-full bg-gradient-to-br from-white to-gray-300 opacity-60 blur-3xl dark:from-zinc-700 dark:to-zinc-900 dark:opacity-40"></div>
                <div class="absolute inset-0 bg-[linear-gradient(to_right,rgba(0,0,0,0.04)_1px,transparent_1px),linear-gradient(to_bottom,rgba(0,0,0,0.04)_1px,transparent_1px)] bg-[size:3rem_3rem] dark:bg-[linear-gradient(to_right,rgba(255,255,255,0.04)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.04)_1px,transparent_1px)]"></div>
            </div>

            <div class="relative flex min-h-screen flex-col">
                <div class="relative w-full max-w-3xl mx-auto px-6 lg:max-w-7xl lg:px-10">
                    <header class="flex items-center justify-between py-10">
                        <a href="/" class="flex items-center gap-3 text-lg font-semibold text-black dark:text-white">
                            <span class="flex size-10 items-center justify-center rounded-full border border-black/20 bg-white/70 text-black shadow-sm backdrop-blur-md dark:border-white/20 dark:bg-white/10 dark:text-white">A</span>
                            {{ config('app.name', 'AboutMe') }}
                        </a>

                        <livewire:welcome.navigation />
                    </header>

                    <main class="flex-1">
                        <section class="grid gap-16 py-16 lg:grid-cols-2 lg:items-center lg:py-28">
                            <div>
                                <h1 class="text-4xl font-bold leading-tight text-black dark:text-white sm:text-5xl lg:text-6xl">
                                    一頁搞定，<br />你的所有連結與自我介紹
                                </h1>

                                <p class="mt-8 max-w-md text-base leading-relaxed text-black/60 dark:text-white/60">
                                    {{ config('app.name', 'AboutMe') }} 是開源、可自架的個人主頁產生器。免費註冊，幾分鐘內就能擁有一個專屬網址，集中展示你的簡介、頭像與所有社群連結。
                                </p>

                                <div class="mt-10 flex flex-wrap items-center gap-4">
                                    @if (Route::has('register'))
                                        <a
                                            href="{{ route('register') }}"
                                            class="rounded-lg border border-black bg-black px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-black/10 transition hover:bg-zinc-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-black focus-visible:ring-offset-2 dark:border-white dark:bg-white dark:text-black dark:shadow-white/10 dark:hover:bg-zinc-200"
                                        >
                                            免費建立我的主頁
                                        </a>
                                    @endif

                                    <a
                                        href="{{ route('login') }}"
                                        class="rounded-lg border border-black/20 bg-white/60 px-6 py-3 text-sm font-semibold text-black backdrop-blur-md transition hover:border-black/40 hover:bg-white/80 dark:border-white/20 dark:bg-white/5 dark:text-white dark:hover:border-white/40 dark:hover:bg-white/10"
                                    >
                                        登入
                                    </a>
                                </div>
                            </div>

                            <div class="rounded-2xl border border-black/15 bg-white/60 p-8 shadow-[0px_20px_50px_0px_rgba(0,0,0,0.12)] backdrop-blur-xl dark:border-white/15 dark:bg-white/5 dark:shadow-[0px_20px_50px_0px_rgba(0,0,0,0.5)]">
                                <div class="mx-auto flex max-w-xs flex-col items-center rounded-xl border border-black/10 bg-white/70 p-8 text-center backdrop-blur-md dark:border-white/10 dark:bg-black/40">
                                    <div class="flex size-20 items-center justify-center rounded-full border border-black/15 bg-black/5 text-2xl font-semibold text-black dark:border-white/15 dark:bg-white/10 dark:text-white">
                                        你
                                    </div>
                                    <p class="mt-4 text-lg font-semibold text-black dark:text-white">你的名字</p>
                                    <p class="text-sm text-black/50 dark:text-white/50">@{{ 'yourname' }}</p>
                                    <p class="mt-3 text-sm leading-relaxed text-black/60 dark:text-white/60">
                                        嗨，這裡是我的所有連結！歡迎逛逛 :)
                                    </p>
                                    <div class="mt-6 grid w-full gap-2">
                                        <span class="rounded-lg border border-black/10 bg-white/80 px-4 py-2 text-sm text-black shadow-sm backdrop-blur-sm dark:border-white/10 dark:bg-white/5 dark:text-white">我的部落格</span>
                                        <span class="rounded-lg border border-black/10 bg-white/80 px-4 py-2 text-sm text-black shadow-sm backdrop-blur-sm dark:border-white/10 dark:bg-white/5 dark:text-white">作品集</span>
                                        <span class="rounded-lg border border-black/10 bg-white/80 px-4 py-2 text-sm text-black shadow-sm backdrop-blur-sm dark:border-white/10 dark:bg-white/5 dark:text-white">聯絡我</span>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="grid gap-6 py-16 sm:grid-cols-3 lg:py-24">
                            <div class="rounded-xl border border-black/15 bg-white/60 p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] backdrop-blur-xl transition hover:border-black/30 dark:border-white/15 dark:bg-white/5 dark:hover:border-white/30">
                                <h2 class="text-lg font-semibold text-black dark:text-white">專屬網址</h2>
                                <p class="mt-3 text-sm leading-relaxed text-black/60 dark:text-white/60">
                                    註冊後即可擁有 <code class="rounded border border-black/10 bg-black/5 px-1 py-0.5 dark:border-white/10 dark:bg-white/10">{{ request()->getHost() }}/你的名稱</code>，一個網址分享所有內容。
                                </p>
                            </div>

                            <div class="rounded-xl border border-black/15 bg-white/60 p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] backdrop-blur-xl transition hover:border-black/30 dark:border-white/15 dark:bg-white/5 dark:hover:border-white/30">
                                <h2 class="text-lg font-semibold text-black dark:text-white">自動辨識社群圖示</h2>
                                <p class="mt-3 text-sm leading-relaxed text-black/60 dark:text-white/60">
                                    貼上 Instagram、GitHub、YouTube 等連結，系統自動顯示對應的品牌圖示。
                                </p>
                            </div>

                            <div class="rounded-xl border border-black/15 bg-white/60 p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] backdrop-blur-xl transition hover:border-black/30 dark:border-white/15 dark:bg-white/5 dark:hover:border-white/30">
                                <h2 class="text-lg font-semibold text-black dark:text-white">開源可自架</h2>
                                <p class="mt-3 text-sm leading-relaxed text-black/60 dark:text-white/60">
                                    完整原始碼公開，任何人都能部署自己的版本，資料完全掌握在自己手上。
                                </p>
                            </div>
                        </section>
                    </main>

                    <footer class="border-t border-black/10 py-10 text-center text-sm text-black/50 dark:border-white/10 dark:text-white/50">
                        {{ config('app.name', 'AboutMe') }} &middot; 開源個人主頁產生器
                    </footer>
                </div>
            </div>
        </div>
    </body>
</html>
