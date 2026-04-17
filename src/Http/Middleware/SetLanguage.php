<?php

namespace LaravelEnso\Localisation\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\App;

class SetLanguage
{
    public function handle($request, Closure $next)
    {
        $language = $request->user()->preferences->lang();

        App::setLocale($language);

        Carbon::setLocale($language);

        return $next($request);
    }
}
