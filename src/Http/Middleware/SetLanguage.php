<?php

namespace LaravelEnso\Localisation\Http\Middleware;

use Carbon\Carbon;
use Closure;

class SetLanguage
{
    public function handle($request, Closure $next)
    {
        $language = $request->user()->preferences->lang();

        app()->setLocale($language);

        Carbon::setLocale($language);

        return $next($request);
    }
}
