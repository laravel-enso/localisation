<?php

namespace LaravelEnso\Localisation\App\Http\Middleware;

use Carbon\Carbon;
use Closure;

class SetLanguage
{
    public function handle($request, Closure $next)
    {
        $language = $request->user()->lang();

        app()->setLocale($language);

        Carbon::setLocale($language);

        return $next($request);
    }
}
