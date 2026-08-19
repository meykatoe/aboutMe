<?php

namespace App\Http\Middleware;

use App\Support\Locale;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * Resolution order: authenticated user's stored locale, then session,
     * then the browser's Accept-Language header (guest default only, not
     * persisted), then the app's configured fallback.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->user()?->locale
            ?? $request->session()->get('locale')
            ?? $this->fromBrowser($request)
            ?? config('app.locale');

        App::setLocale($locale);

        return $next($request);
    }

    protected function fromBrowser(Request $request): ?string
    {
        $preferred = $request->getPreferredLanguage(array_keys(Locale::supported()));

        return $preferred && Locale::isSupported($preferred) ? $preferred : null;
    }
}
