<?php

use App\User;
use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use LaravelEnso\Core\app\Classes\DefaultPreferences;
use LaravelEnso\Core\app\Models\Preference;
use LaravelEnso\Localisation\app\Models\Language;
use LaravelEnso\TestHelper\app\Classes\TestHelper;

class LocalisationTest extends TestHelper
{
    use DatabaseMigrations;

    private $faker;
    private $name;

    protected function setUp()
    {
        parent::setUp();

        // $this->disableExceptionHandling();
        $this->faker = Factory::create();
        $this->name = strtolower($this->faker->countryCode);
        $this->signIn(User::first());
    }

    /** @test */
    public function index()
    {
        $this->get('/system/localisation')
            ->assertStatus(200)
            ->assertViewIs('laravel-enso/localisation::index');
    }

    /** @test */
    public function create()
    {
        $this->get('/system/localisation/create')
            ->assertStatus(200)
            ->assertViewIs('laravel-enso/localisation::create')
            ->assertViewHas('form');
    }

    /** @test */
    public function store()
    {
        $response = $this->post(
            '/system/localisation', $this->postParams()
        );

        $language = Language::whereName($this->name)->first();

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message'  => 'The language was created!',
                'redirect' => '/system/localisation/'.$language->id.'/edit',
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
    public function edit()
    {
        $language = $this->createLanguage();

        $response = $this->get(
            route('system.localisation.edit', $language->id, false))
            ->assertStatus(200)
            ->assertViewIs('laravel-enso/localisation::edit')
            ->assertViewHas('form');

        $this->cleanUp($language);
    }

    /** @test */
    public function update()
    {
        $this->post(
            route('system.localisation.store', [], false),
            $this->postParams()
        );
        $language = Language::whereName($this->name)->first();

        $language->name = 'xx';

        $this->patch(
            route('system.localisation.update', $language->id, false),
            $language->toArray() + ['flag_sufix' => $language->name]
        )->assertStatus(200)
            ->assertJson([
                'message' => 'The changes have been saved',
            ]);

        $this->assertEquals('xx', $language->fresh()->name);
        $this->assertTrue(
            \File::exists(resource_path('lang/'.$language->name))
        );
        $this->assertTrue(
            \File::exists(resource_path('lang/'.$language->name.'.json'))
        );

        $this->cleanUp($language);
    }

    /** @test */
    public function destroy()
    {
        $this->post(
            route('system.localisation.store', [], false),
            $this->postParams()
        );
        $language = Language::whereName($this->name)->first();
        $languageName = $language->name;

        $this->delete(
            route('system.localisation.destroy', $language->id, false)
        )->assertStatus(200)
            ->assertJson([
                'message'  => 'The operation was successful',
                'redirect' => '/system/localisation',
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
        $language = $this->createLanguage();
        config()->set('app.fallback_locale', $language->name);

        $this->delete(route('system.localisation.destroy', $language->id, false))
            ->assertStatus(455);

        $this->assertNotNull($language->fresh());

        $this->cleanUp($language);
    }

    /** @test */
    public function cant_destroy_if_language_is_in_use()
    {
        $this->post(
            route('system.localisation.store', [], false),
            $this->postParams()
        );
        $language = Language::whereName($this->name)->first();

        $this->setLanguage($language);

        $this->delete(route('system.localisation.destroy', $language->id, false))
            ->assertStatus(455);

        $this->assertTrue(
            \File::exists(resource_path('lang/'.$language->name))
        );
        $this->assertTrue(
            \File::exists(resource_path('lang/'.$language->name.'.json'))
        );

        $this->cleanUp($language);
    }

    private function createLanguage()
    {
        return Language::create($this->postParams());
    }

    private function postParams()
    {
        return [
            'display_name' => strtolower($this->faker->country),
            'name'         => $this->name,
            'flag_sufix'   => $this->name,
            'flag'         => 'flag-icon flag-icon-'.$this->name,
        ];
    }

    private function setLanguage($language)
    {
        $preferences = (new DefaultPreferences())->getData();
        $preferences->global->lang = $language->name;
        $preference = new Preference(['value' => $preferences]);
        $preference->user_id = 1;
        $preference->save();
    }

    private function cleanUp($language)
    {
        \File::delete(
            resource_path('lang'.DIRECTORY_SEPARATOR.$language->name.'.json')
        );
        \File::deleteDirectory(
            resource_path('lang'.DIRECTORY_SEPARATOR.$language->name)
        );
    }
}
