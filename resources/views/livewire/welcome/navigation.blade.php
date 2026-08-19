<nav class="-mx-3 flex flex-1 items-center justify-end gap-1">
    @auth
        <a
            href="{{ url('/dashboard') }}"
            class="rounded-lg border border-transparent px-3 py-2 text-black transition hover:border-black/15 hover:bg-white/60 focus:outline-none focus-visible:ring-2 focus-visible:ring-black dark:text-white dark:hover:border-white/15 dark:hover:bg-white/10 dark:focus-visible:ring-white"
        >
            Dashboard
        </a>
    @else
        <a
            href="{{ route('login') }}"
            class="rounded-lg border border-transparent px-3 py-2 text-black transition hover:border-black/15 hover:bg-white/60 focus:outline-none focus-visible:ring-2 focus-visible:ring-black dark:text-white dark:hover:border-white/15 dark:hover:bg-white/10 dark:focus-visible:ring-white"
        >
            Log in
        </a>

        @if (Route::has('register'))
            <a
                href="{{ route('register') }}"
                class="rounded-lg border border-transparent px-3 py-2 text-black transition hover:border-black/15 hover:bg-white/60 focus:outline-none focus-visible:ring-2 focus-visible:ring-black dark:text-white dark:hover:border-white/15 dark:hover:bg-white/10 dark:focus-visible:ring-white"
            >
                Register
            </a>
        @endif
    @endauth
</nav>
