<?php

namespace LaravelEnso\Localisation\app\Http\Middleware;

use Closure;
use Jenssegers\Date\Date;

class SetLanguage
{
    public function handle($request, Closure $next)
    {
        $language = $request->user()->lang();

        app()->setLocale($language);

        Date::setLocale($language);

        return $next($request);
    }
}
