<?php

namespace LaravelEnso\Localisation\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use LaravelEnso\DataTable\Traits\DataTable;
use LaravelEnso\Localisation\DataTable\LocalisationTableStructure;
use LaravelEnso\Localisation\Http\Requests\ValidateLanguageRequest;
use LaravelEnso\Localisation\Language;
use LaravelEnso\Select\Traits\SelectListBuilderTrait;

class LocalisationController extends Controller
{

    use DataTable, SelectListBuilderTrait;

    protected $tableStructureClass = LocalisationTableStructure::class;

    public static function getTableQuery()
    {
        return Language::select(\DB::raw('languages.id as DT_RowId, languages.display_name, languages.name, languages.flag, languages.created_at, languages.updated_at'));
    }

    public function index()
    {
        return view('localisation::index');
    }

    public function create()
    {
        return view('localisation::create');
    }

    public function store(ValidateLanguageRequest $request, Language $localisation)
    {
        \DB::transaction(function () use ($localisation) {

            $localisation->fill($request->all());
            $locale   = Language::allExceptDefault()->first();
            $langFile = (array) $this->readLangFileContent($locale->name);
            $langFile = $this->clearArrayValues($langFile);

            \File::put(resource_path('lang/' . $localisation->name . '.json'), json_encode($langFile));

            $localisation->save();
        });

        flash()->success(__('Language Created'));

        return redirect('system/localisation/' . $localisation->id . '/edit');
    }

    private function clearArrayValues($array)
    {
        $keys     = array_keys($array);
        $values   = array_fill(0, count($keys), null);
        $newArray = array_combine($keys, $values);

        return $newArray;
    }

    public function edit(Language $localisation)
    {

        return view('localisation::edit', compact('localisation'));
    }

    public function update(ValidateLanguageRequest $request, Language $localisation)
    {
        $localisation->fill($request->all())->save();

        flash()->success(__("The Changes have been saved!"));

        return back();
    }

    public function editTexts()
    {
        $languages = Language::allExceptDefault()->get()->pluck('display_name', 'name');

        $languaguesList = $this->buildSelectList($languages);

        return view('localisation::editTexts', compact('languaguesList'));
    }

    public function destroy(Language $localisation)
    {
        \File::delete(resource_path('lang/' . $localisation->name . '.json'));

        $localisation->delete();

        return [
            'level'   => 'success',
            'message' => __("Operation was successfull"),
        ];
    }

    public function getLangFile($locale)
    {
        $content = $this->readLangFileContent($locale);

        return response()->json($content);
    }

    private function readLangFileContent($locale)
    {
        return json_decode(\File::get(resource_path('lang/' . $locale . '.json')));
    }

    public function saveLangFile()
    {
        $this->saveToDisk(request()->langFile, request()->locale);

        $languages = Language::allExceptDefault()->where('name', '<>', request()->locale)->get()->pluck('name');

        foreach ($languages as $locale) {

            $this->updateLocalisationFile($locale);
        }

        return [

            'level'   => 'success',
            'message' => __("Operation was successfull"),
        ];
    }

    private function saveToDisk($langFile, $locale)
    {
        \File::put(resource_path('lang/' . $locale . '.json'), json_encode($langFile));
    }

    private function updateLocalisationFile($locale)
    {
        $langFile     = (array) $this->readLangFileContent($locale);
        $keysToAdd    = array_diff_key(request()->langFile, $langFile);
        $keysToAdd    = $this->clearArrayValues($keysToAdd);
        $keysToRemove = array_diff_key($langFile, request()->langFile);

        foreach (array_keys($keysToRemove) as $keyToRemove) {

            unset($langFile[$keyToRemove]);
        }

        $langFile = array_merge($keysToAdd, $langFile);

        if (count($keysToAdd) || count($keysToRemove)) {

            $this->saveToDisk($langFile, $locale);
        }
    }
}
