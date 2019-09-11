<?php

namespace LaravelEnso\Localisation\app\Http\Controllers\Language;

use Illuminate\Routing\Controller;
use LaravelEnso\Localisation\app\Models\Language;
use LaravelEnso\Localisation\app\Services\Destroyer;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Destroy extends Controller
{
    use AuthorizesRequests;

    public function __invoke(Language $localisation)
    {
        $this->authorize('destroy', $localisation);

        (new Destroyer($localisation))->run();

        return [
            'message' => __('The language was successfully deleted'),
            'redirect' => 'system.localisation.index',
        ];
    }
}
