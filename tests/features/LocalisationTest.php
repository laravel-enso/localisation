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
        $this->name  = strtolower($this->faker->countryCode);
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
        $response = $this->post('/system/localisation', $this->postParams());

        $language = Language::whereName($this->name)->first();

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message'  => 'The language was created!',
                'redirect' => '/system/localisation/' . $language->id . '/edit',
            ]);

        $this->assertTrue(\File::exists(resource_path('lang/' . $language->name)));
        $this->assertTrue(\File::exists(resource_path('lang/' . $language->name . '.json')));

        $this->cleanUp($language);
    }

    /** @test */
    public function edit()
    {
        $language = $this->createLanguage();

        $response = $this->get('/system/localisation/' . $language->id . '/edit')
            ->assertStatus(200)
            ->assertViewIs('laravel-enso/localisation::edit')
            ->assertViewHas('form');

        $this->cleanUp($language);
    }

    /** @test */
    public function update()
    {
        $response = $this->post('/system/localisation', $this->postParams());
        $language = Language::whereName($this->name)->first();

        $language->name = 'xx';

        $this->patch(
                '/system/localisation/' . $language->id,
                $language->toArray() + ['flag_sufix' => $language->name]
            )->assertStatus(200)
            ->assertJson(['message' => __(config('labels.savedChanges'))]);

        $this->assertEquals($language->name, $language->fresh()->name);
        $this->assertTrue(\File::exists(resource_path('lang/' . $language->name)));
        $this->assertTrue(\File::exists(resource_path('lang/' . $language->name . '.json')));

        $this->cleanUp($language);
    }

    /** @test */
    public function destroy()
    {
        $this->post('/system/localisation', $this->postParams());
        $language = Language::whereName($this->name)->first();

        $this->delete('/system/localisation/' . $language->id)
            ->assertStatus(200)
            ->assertJsonFragment(['message']);

        $this->assertFalse(\File::exists(resource_path('lang/' . $language->name)));
        $this->assertFalse(\File::exists(resource_path('lang/' . $language->name . '.json')));
    }

    /** @test */
    public function cant_destroy_default_language()
    {
        $language = Language::whereName(config('app.fallback_locale'))->first();

        $this->delete('/system/localisation/' . $language->id)
            ->assertSessionHas('flash_notification');

        $this->assertEquals($language, $language->fresh());
    }

    /** @test */
    public function cant_destroy_if_language_is_in_use()
    {
        $this->post('/system/localisation', $this->postParams());
        $language = Language::whereName($this->name)->first();
        $this->setLanguage($language);

        $this->delete('/system/localisation/' . $language->id)
            ->assertStatus(302)
            ->assertSessionHas('flash_notification');

        $this->assertTrue(\File::exists(resource_path('lang/' . $language->name)));
        $this->assertTrue(\File::exists(resource_path('lang/' . $language->name . '.json')));

        $this->cleanUp($language);
    }

    private function cleanUp($language)
    {
        \File::delete(resource_path('lang' . DIRECTORY_SEPARATOR . $language->name . '.json'));
        \File::deleteDirectory(resource_path('lang' . DIRECTORY_SEPARATOR . $language->name));
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
            'flag'         => 'flag-icon flag-icon-' . $this->name,
        ];
    }

    private function setLanguage($language)
    {
        $preferences               = (new DefaultPreferences())->getData();
        $preferences->global->lang = $language->name;
        $preference                = new Preference(['value' => $preferences]);
        $preference->user_id       = 1;
        $preference->save();
    }
}
