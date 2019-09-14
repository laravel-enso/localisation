<?php

use Tests\TestCase;
use LaravelEnso\Core\app\Models\User;
use LaravelEnso\Core\app\Models\Preference;
use LaravelEnso\Forms\app\TestTraits\EditForm;
use LaravelEnso\Forms\app\TestTraits\CreateForm;
use LaravelEnso\Forms\app\TestTraits\DestroyForm;
use LaravelEnso\Localisation\app\Models\Language;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LaravelEnso\Tables\app\Traits\Tests\Datatable;
use LaravelEnso\Core\app\Services\DefaultPreferences;

class LocalisationTest extends TestCase
{
    use CreateForm, Datatable, DestroyForm, EditForm, RefreshDatabase;

    private const LangName = 'xx';

    private $permissionGroup = 'system.localisation';
    private $testModel;
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        // $this->withoutExceptionHandling();

        $this->seed()
            ->actingAs($this->user = User::first());

        $this->testModel = factory(Language::class)->make([
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
                'message' => __('The language was successfully created'),
                'redirect' => 'system.localisation.edit',
                'param' => ['language' => $language->id],
            ]);

        $this->assertTrue(
            \File::exists(resource_path('lang/'.$language->name))
        );

        $this->assertTrue(
            \File::exists(resource_path('lang/'.$language->name.'.json'))
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
            \File::exists(resource_path('lang/'.$language->name))
        );

        $this->assertTrue(
            \File::exists(resource_path('lang/'.$language->name.'.json'))
        );

        $this->cleanUp($language);
    }

    /** @test */
    public function can_destroy_language()
    {
        $this->post(
            route('system.localisation.store', [], false),
            $this->testModel->toArray() + [
                'flag_sufix' => self::LangName,
            ]);

        $language = Language::whereName(self::LangName)->first();
        $languageName = $language->name;

        $this->delete(
            route('system.localisation.destroy', $language->id, false)
        )->assertStatus(200)
        ->assertJson([
            'message' => __('The language was successfully deleted'),
            'redirect' => 'system.localisation.index',
        ]);

        $this->assertFalse(
            \File::exists(resource_path('lang/'.$languageName))
        );

        $this->assertFalse(
            \File::exists(resource_path('lang/'.$languageName.'.json'))
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
            ]);

        $language = Language::whereName(self::LangName)->first();

        $this->setLanguage($language);

        $this->delete(route('system.localisation.destroy', $language->id, false))
            ->assertStatus(403);

        $this->assertTrue(
            \File::exists(resource_path('lang/'.$language->name))
        );

        $this->assertTrue(
            \File::exists(resource_path('lang/'.$language->name.'.json'))
        );

        $this->cleanUp($language);
    }

    private function setLanguage($language)
    {
        $preferences = DefaultPreferences::data();
        $preferences->global->lang = $language->name;

        Preference::create([
            'user_id' => $this->user->id,
            'value' => $preferences,
        ]);
    }

    private function cleanUp($language)
    {
        \File::delete(
            resource_path('lang'.DIRECTORY_SEPARATOR.$language->name.'.json')
        );

        \File::delete(
            resource_path('lang/app'.DIRECTORY_SEPARATOR.$language->name.'.json')
        );

        \File::delete(
            resource_path('lang/enso'.DIRECTORY_SEPARATOR.$language->name.'.json')
        );

        \File::deleteDirectory(
            resource_path('lang'.DIRECTORY_SEPARATOR.$language->name)
        );
    }
}
