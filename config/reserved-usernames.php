<?php

return [
    // Existing application routes — registering one of these as a username
    // would make that route unreachable, or make the user's public page
    // unreachable (whichever is registered/matched first).
    'login',
    'logout',
    'register',
    'dashboard',
    'profile',
    'links',
    'forgot-password',
    'reset-password',
    'verify-email',
    'confirm-password',

    // Served directly from the public/ directory, not through the router,
    // but still worth reserving to avoid confusing overlaps.
    'storage',
    'build',
    'favicon.ico',
    'robots.txt',

    // Reserved for likely future routes (e.g. an operator-facing admin
    // panel) and generic terms that would be confusing as a personal page.
    'admin',
    'api',
    'www',
    'app',
    'assets',
    'public',
    'settings',
    'support',
    'help',
    'about',
    'terms',
    'privacy',
    'security',
    'null',
    'undefined',
];
