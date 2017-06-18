<?php

namespace LaravelEnso\Localisation\app\Http\Middleware;

use Closure;

class SetLanguage
{
    public function handle($request, Closure $next)
    {
        app()->setLocale($request->user()->preferences->global->lang);

        return $next($request);
    }
}
