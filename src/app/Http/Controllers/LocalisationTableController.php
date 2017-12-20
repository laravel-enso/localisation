<?php

namespace LaravelEnso\Localisation\app\Http\Controllers;

use App\Http\Controllers\Controller;
use LaravelEnso\VueDatatable\app\Traits\Excel;
use LaravelEnso\Localisation\app\Models\Language;
use LaravelEnso\VueDatatable\app\Traits\Datatable;

class LocalisationTableController extends Controller
{
    use Datatable, Excel;

    private const Template = __DIR__.'/../../Tables/localisation.json';

    public function query()
    {
        return Language::select(\DB::raw(
            'languages.id as dtRowId, languages.display_name, languages.name,
            languages.flag, languages.created_at, languages.updated_at'
        ));
    }
}
