<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use LaravelEnso\Core\Models\Preference;
use LaravelEnso\Core\Services\DefaultPreferences;
use LaravelEnso\Forms\TestTraits\CreateForm;
use LaravelEnso\Forms\TestTraits\DestroyForm;
use LaravelEnso\Forms\TestTraits\EditForm;
use LaravelEnso\Localisation\Models\Language;
use LaravelEnso\Tables\Traits\Tests\Datatable;
use LaravelEnso\Users\Models\User;
use Tests\TestCase;

class LocalisationTest extends TestCase
{
    use CreateForm;
    use Datatable;
    use DestroyForm;
    use EditForm;
    use RefreshDatabase;

    private const LangName = 'xx';

    private $permissionGroup = 'system.localisation';
    private $testModel;
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed()
            ->actingAs($this->user = User::first());

        $this->testModel = Language::factory()->make([
            'name' => self::LangName,
            'flag' => 'flag-icon flag-icon-'.self::LangName,
        ]);
    }

    /** @test */
    public function can_store_language()
    {
        $response = $this->post(
            route('system.localisation.store'),
            $this->testModel->toArray() + ['flag_sufix' => self::LangName]
        );

        $language = Language::whereName(self::LangName)->first();

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message'  => __('The language was successfully created'),
                'redirect' => 'system.localisation.edit',
                'param'    => ['language' => $language->id],
            ]);

        $this->assertTrue(
            File::exists(App::langPath($language->name))
        );

        $this->assertTrue(
            File::exists(App::langPath($language->name.'.json'))
        );

        $this->cleanUp($language);
    }

    /** @test */
    public function can_update_language()
    {
        $this->post(
            route('system.localisation.store', [], false),
            $this->testModel->toArray() + ['flag_sufix' => self::LangName]
        );

        $language = Language::whereName(self::LangName)->first();

        $language->name = 'zz';

        $this->patch(
            route('system.localisation.update', $language->id, false),
            $language->toArray() + ['flag_sufix' => $language->name]
        )->assertStatus(200)
            ->assertJson([
                'message' => __('The language was successfully updated'),
            ]);

        $this->assertEquals('zz', $language->fresh()->name);

        $this->assertTrue(
            File::exists(App::langPath($language->name))
        );

        $this->assertTrue(
            File::exists(App::langPath($language->name.'.json'))
        );

        $this->cleanUp($language);

        File::delete(
            base_path('vendor/laravel-enso/localisation/lang/enso/'.$language->name.'.json')
        );
    }

    /** @test */
    public function can_destroy_language()
    {
        $this->post(
            route('system.localisation.store', [], false),
            $this->testModel->toArray() + [
                'flag_sufix' => self::LangName,
            ]
        );

        $language = Language::whereName(self::LangName)->first();
        $languageName = $language->name;

        $this->delete(
            route('system.localisation.destroy', $language->id, false)
        )->assertStatus(200)
            ->assertJson([
                'message'  => __('The language was successfully deleted'),
                'redirect' => 'system.localisation.index',
            ]);

        $this->assertFalse(
            File::exists(App::langPath($languageName))
        );

        $this->assertFalse(
            File::exists(App::langPath($languageName.'.json'))
        );
    }

    /** @test */
    public function cant_destroy_default_language()
    {
        $this->testModel->save();

        config()->set(
            'app.fallback_locale',
            $this->testModel->name
        );

        $this->delete(route('system.localisation.destroy', $this->testModel->id, false))
            ->assertStatus(403);

        $this->assertNotNull($this->testModel->fresh());
    }

    /** @test */
    public function cant_destroy_if_language_is_in_use()
    {
        $this->post(
            route('system.localisation.store', [], false),
            $this->testModel->toArray() + [
                'flag_sufix' => self::LangName,
            ]
        );

        $language = Language::whereName(self::LangName)->first();

        $this->setLanguage($language);

        $this->delete(route('system.localisation.destroy', $language->id, false))
            ->assertStatus(403);

        $this->assertTrue(
            File::exists(App::langPath($language->name))
        );

        $this->assertTrue(
            File::exists(App::langPath($language->name.'.json'))
        );

        $this->cleanUp($language);
    }

    private function setLanguage($language)
    {
        $preferences = DefaultPreferences::data();
        $preferences->global->lang = $language->name;

        Preference::create([
            'user_id' => $this->user->id,
            'value'   => $preferences,
        ]);
    }

    private function cleanUp($language)
    {
        File::delete(
            App::langPath($language->name.'.json')
        );

        File::delete(
            App::langPath('app'.DIRECTORY_SEPARATOR.$language->name.'.json')
        );

        File::delete(
            App::langPath('enso'.DIRECTORY_SEPARATOR.$language->name.'.json')
        );

        File::deleteDirectory(
            App::langPath($language->name)
        );
    }
}
