<?php

namespace LaravelEnso\Localisation\app\Http\Controllers;

use App\Http\Controllers\Controller;
use LaravelEnso\DataTable\app\Traits\DataTable;
use LaravelEnso\Localisation\app\DataTable\LocalisationTableStructure;
use LaravelEnso\Localisation\app\Models\Language;

class LocalisationTableController extends Controller
{
    use DataTable;

    protected $tableStructureClass = LocalisationTableStructure::class;

    public function getTableQuery()
    {
        return Language::select(\DB::raw('languages.id as DT_RowId, languages.display_name,
            languages.name, languages.flag, languages.created_at, languages.updated_at'));
    }
}
