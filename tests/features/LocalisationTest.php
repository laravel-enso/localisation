<?php

use App\User;
use Faker\Factory;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use LaravelEnso\Core\app\Classes\DefaultPreferences;
use LaravelEnso\Core\app\Models\Preference;
use LaravelEnso\Localisation\app\Models\Language;
use LaravelEnso\TestHelper\app\Traits\SignIn;
use LaravelEnso\TestHelper\app\Traits\TestCreateForm;
use LaravelEnso\TestHelper\app\Traits\TestDataTable;
use Tests\TestCase;

class LocalisationTest extends TestCase
{
    use DatabaseMigrations, SignIn, TestDataTable, TestCreateForm;

    private $faker;
    private $name;
    private $prefix = 'system.localisation';

    protected function setUp()
    {
        parent::setUp();

        // $this->withoutExceptionHandling();
        $this->faker = Factory::create();
        $this->name  = strtolower($this->faker->countryCode);
        $this->signIn(User::first());
    }

    /** @test */
    public function store()
    {
        $response = $this->post(route('system.localisation.store', [], false), $this->postParams());

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

        $this->get(route('system.localisation.edit', $language->id, false))
            ->assertStatus(200)
            ->assertJsonStructure(['form']);

        $this->cleanUp($language);
    }

    /** @test */
    public function update()
    {
        $this->post(route('system.localisation.store', [], false), $this->postParams());
        $language = Language::whereName($this->name)->first();

        $language->name = 'xx';

        $this->patch(
            route('system.localisation.update', $language->id, false),
            $language->toArray() + ['flag_sufix' => $language->name]
        )->assertStatus(200)
            ->assertJson(['message' => __(config('enso.labels.savedChanges'))]);

        $this->assertEquals($language->name, $language->fresh()->name);
        $this->assertTrue(\File::exists(resource_path('lang/' . $language->name)));
        $this->assertTrue(\File::exists(resource_path('lang/' . $language->name . '.json')));

        $this->cleanUp($language);
    }

    /** @test */
    public function destroy()
    {
        $this->post(route('system.localisation.store', [], false), $this->postParams());
        $language = Language::whereName($this->name)->first();

        $this->delete(route('system.localisation.destroy', $language->id, false))
            ->assertStatus(200)
            ->assertJsonFragment(['message']);

        $this->assertFalse(\File::exists(resource_path('lang/' . $language->name)));
        $this->assertFalse(\File::exists(resource_path('lang/' . $language->name . '.json')));
    }

    /** @test */
    public function cant_destroy_default_language()
    {
        $language = Language::whereName(config('app.fallback_locale'))->first();

        $this->expectException(AuthorizationException::class);

        $this->delete(route('system.localisation.destroy', $language->id, false))
            ->assertStatus(403)
            ->assertJsonStructure(['message']);

        $this->assertEquals($language, $language->fresh());
    }

    /** @test */
    public function cant_destroy_if_language_is_in_use()
    {
        $this->post(route('system.localisation.store', [], false), $this->postParams());
        $language = Language::whereName($this->name)->first();
        $this->setLanguage($language);

        $this->delete(route('system.localisation.destroy', $language->id, false))
            ->assertStatus(403)
            ->assertJsonStructure(['message']);

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
