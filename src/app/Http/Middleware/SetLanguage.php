<?php

namespace LaravelEnso\Localisation\app\Http\Middleware;

use Closure;

class SetLanguage
{
    public function handle($request, Closure $next)
    {
        $language = $request->user()->lang();

        app()->setLocale($language);

        return $next($request);
    }
}
