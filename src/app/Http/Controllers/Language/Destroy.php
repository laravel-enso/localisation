<?php

namespace LaravelEnso\Localisation\App\Http\Controllers\Language;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;
use LaravelEnso\Localisation\App\Models\Language;
use LaravelEnso\Localisation\App\Services\Destroyer;

class Destroy extends Controller
{
    use AuthorizesRequests;

    public function __invoke(Language $language)
    {
        $this->authorize('destroy', $language);

        (new Destroyer($language))->run();

        return [
            'message' => __('The language was successfully deleted'),
            'redirect' => 'system.localisation.index',
        ];
    }
}
