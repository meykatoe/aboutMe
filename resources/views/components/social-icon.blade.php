@props(['platform' => 'link'])

<span {{ $attributes->merge(['class' => 'inline-flex']) }}>
    @switch($platform)
        @case('twitter')
            <svg viewBox="0 0 24 24" fill="currentColor" class="w-full h-full">
                <path d="M18.9 3H21l-6.5 7.4L22.2 21h-6.4l-5-6.5L5 21H2.9l6.9-7.9L1.9 3h6.5l4.5 6 6-6Zm-1.1 16.2h1.7L7.3 4.7H5.5l12.3 14.5Z"/>
            </svg>
        @break

        @case('instagram')
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="w-full h-full">
                <rect x="3" y="3" width="18" height="18" rx="5"/>
                <circle cx="12" cy="12" r="4.2"/>
                <circle cx="17.2" cy="6.8" r="1.1" fill="currentColor" stroke="none"/>
            </svg>
        @break

        @case('github')
            <svg viewBox="0 0 24 24" fill="currentColor" class="w-full h-full">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.48 2 2 6.58 2 12.2c0 4.5 2.87 8.32 6.84 9.67.5.1.68-.22.68-.49 0-.24-.01-.88-.01-1.72-2.78.62-3.37-1.37-3.37-1.37-.46-1.2-1.11-1.52-1.11-1.52-.9-.63.07-.62.07-.62 1 .07 1.53 1.05 1.53 1.05.89 1.55 2.33 1.11 2.9.85.09-.66.35-1.11.63-1.37-2.22-.26-4.56-1.14-4.56-5.07 0-1.12.39-2.03 1.03-2.75-.1-.26-.45-1.31.1-2.73 0 0 .84-.28 2.75 1.05a9.3 9.3 0 0 1 5 0c1.91-1.33 2.75-1.05 2.75-1.05.55 1.42.2 2.47.1 2.73.64.72 1.03 1.63 1.03 2.75 0 3.94-2.35 4.8-4.58 5.06.36.32.68.94.68 1.9 0 1.37-.01 2.47-.01 2.81 0 .27.18.6.69.49A10.02 10.02 0 0 0 22 12.2C22 6.58 17.52 2 12 2Z"/>
            </svg>
        @break

        @case('linkedin')
            <svg viewBox="0 0 24 24" fill="currentColor" class="w-full h-full">
                <path d="M4.98 3.5a2.5 2.5 0 1 0 .02 5 2.5 2.5 0 0 0-.02-5ZM3 9.75h4V21H3V9.75Zm7 0h3.8v1.54h.05c.53-.98 1.83-2.02 3.76-2.02 4.02 0 4.76 2.53 4.76 5.83V21h-4v-5.4c0-1.29-.02-2.94-1.8-2.94-1.8 0-2.08 1.4-2.08 2.85V21h-4V9.75Z"/>
            </svg>
        @break

        @case('facebook')
            <svg viewBox="0 0 24 24" fill="currentColor" class="w-full h-full">
                <path d="M13.5 21v-7.7h2.6l.4-3h-3v-1.9c0-.87.24-1.46 1.5-1.46h1.6V4.24C15.9 4.16 15 4.1 14 4.1c-2.5 0-4.2 1.5-4.2 4.3v2h-2.6v3h2.6V21h3.7Z"/>
            </svg>
        @break

        @case('youtube')
            <svg viewBox="0 0 24 24" fill="currentColor" class="w-full h-full">
                <path d="M21.6 7.3a2.7 2.7 0 0 0-1.9-1.9C18 5 12 5 12 5s-6 0-7.7.4A2.7 2.7 0 0 0 2.4 7.3 27.9 27.9 0 0 0 2 12a27.9 27.9 0 0 0 .4 4.7 2.7 2.7 0 0 0 1.9 1.9C6 19 12 19 12 19s6 0 7.7-.4a2.7 2.7 0 0 0 1.9-1.9A27.9 27.9 0 0 0 22 12a27.9 27.9 0 0 0-.4-4.7ZM10 15.3V8.7l5.6 3.3-5.6 3.3Z"/>
            </svg>
        @break

        @case('tiktok')
            <svg viewBox="0 0 24 24" fill="currentColor" class="w-full h-full">
                <path d="M16.5 3h-3v12.3a2.4 2.4 0 1 1-2-2.36V9.85a5.5 5.5 0 1 0 5 5.47V9.1a6.9 6.9 0 0 0 4 1.28V7.3a3.9 3.9 0 0 1-4-4.3Z"/>
            </svg>
        @break

        @case('threads')
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="w-full h-full">
                <path d="M12 3c-4.5 0-7.5 2.8-7.5 9s3 9 7.5 9c3.7 0 6-1.9 6-4.8 0-2.3-1.6-3.7-4.3-3.7-2 0-3.4.9-3.4 2.4 0 1.1.9 1.8 2.1 1.8.9 0 1.6-.4 2-1"/>
                <path d="M9.5 9.3c.4-1 1.4-1.6 2.7-1.6 2 0 3.3 1.3 3.3 3.6"/>
            </svg>
        @break

        @default
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="w-full h-full">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.5 14.5 14.5 9.5M8 12a4 4 0 0 1 0-5.7l1-1a4 4 0 0 1 5.7 5.7M16 12a4 4 0 0 1 0 5.7l-1 1a4 4 0 0 1-5.7-5.7"/>
            </svg>
    @endswitch
</span>
