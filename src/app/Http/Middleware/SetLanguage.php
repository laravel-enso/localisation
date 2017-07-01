<?php

namespace LaravelEnso\Localisation\app\Http\Middleware;

use Closure;

class SetLanguage
{
    public function handle($request, Closure $next)
    {
    	$language = $request->user()->getLanguage();
        app()->setLocale($language);
        \Date::setLocale($language);

        return $next($request);
    }
}
