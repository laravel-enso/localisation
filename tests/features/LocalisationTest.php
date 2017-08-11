<?php

use App\User;
use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use LaravelEnso\Core\app\Classes\DefaultPreferences;
use LaravelEnso\Core\app\Models\Preference;
use LaravelEnso\Localisation\app\Models\Language;
use Tests\TestCase;

class LocalisationTest extends TestCase
{
    use DatabaseMigrations;

    private $faker;

    protected function setUp()
    {
        parent::setUp();

        // $this->disableExceptionHandling();
        $this->faker = Factory::create();
        $this->actingAs(User::first());
    }

    /** @test */
    public function index()
    {
        $response = $this->get('/system/localisation');

        $response->assertStatus(200);
    }

    /** @test */
    public function create()
    {
        $response = $this->get('/system/localisation/create');

        $response->assertStatus(200);
    }

    /** @test */
    public function store()
    {
        $name = $this->faker->countryCode;

        $response = $this->post('/system/localisation', [
            'display_name' => $this->faker->country,
            'name'         => $name,
        ]);

        $language = Language::whereName($name)->first();

        $response->assertStatus(200)
            ->assertJsonFragment([
            'message' => 'The language was created!',
            'redirect'=>'/system/localisation/'.$language->id.'/edit'
        ]);
        $this->assertTrue(\File::exists(resource_path('lang/'.$language->name)));
        $this->assertTrue(\File::exists(resource_path('lang/'.$language->name.'.json')));

        $this->cleanUp($language);
    }

    /** @test */
    public function edit()
    {
        $language = $this->createNewLanguage();

        $response = $this->get('/system/localisation/'.$language->id.'/edit');

        $response->assertStatus(200);
        $response->assertViewHas('form');
        $this->cleanUp($language);
    }

    /** @test */
    public function update()
    {
        $language = $this->createNewLanguage();
        $language->name = 'edited';
        $language->_method = 'PATCH';

        $response = $this->patch('/system/localisation/'.$language->id, $language->toArray());

        $response = $this->patch('/system/localisation/'.$language->id, $language->toArray())
            ->assertStatus(200)
            ->assertJson(['message' => __(config('labels.savedChanges'))]);

        $this->assertTrue($language->fresh()->name === 'edited');
        $this->assertTrue(\File::exists(resource_path('lang/'.$language->name)));
        $this->assertTrue(\File::exists(resource_path('lang/'.$language->name.'.json')));
        $this->cleanUp($language);
    }

    /** @test */
    public function destroy()
    {
        $language = $this->createNewLanguage();

        $response = $this->delete('/system/localisation/'.$language->id);

        $response->assertStatus(200);
        $response->assertJsonFragment(['message']);
        $this->assertFalse(\File::exists(resource_path('lang/'.$language->name)));
        $this->assertFalse(\File::exists(resource_path('lang/'.$language->name.'.json')));
    }

    /** @test */
    public function cantDestroyDefaultLanguage()
    {
        $language = Language::whereName(config('app.fallback_locale'))->first();

        $this->delete('/system/localisation/'.$language->id);

        $this->assertTrue(session('flash_notification')[0]->level === 'danger');
        $this->assertEquals($language, $language->fresh());
    }

    /** @test */
    public function cantDestroyIfLanguageIsInUse()
    {
        $language = $this->createNewLanguage();
        $this->setLanguage($language);

        $this->delete('/system/localisation/'.$language->id);

        $this->assertTrue(session('flash_notification')[0]->level === 'danger');
        $this->assertTrue(\File::exists(resource_path('lang/'.$language->name)));
        $this->assertTrue(\File::exists(resource_path('lang/'.$language->name.'.json')));
        $this->cleanUp($language);
    }

    private function cleanUp($language)
    {
        \File::delete(resource_path('lang'.DIRECTORY_SEPARATOR.$language->name.'.json'));
        \File::deleteDirectory(resource_path('lang'.DIRECTORY_SEPARATOR.$language->name));
    }

    private function createNewLanguage()
    {
        $name = strtolower($this->faker->countryCode);
        $this->post('/system/localisation', [
            'display_name' => strtolower($this->faker->country),
            'name'         => $name,
        ]);
        $language = Language::whereName($name)->first();

        return $language;
    }

    private function setLanguage($language)
    {
        $preferences = (new DefaultPreferences())->getData();
        $preferences->global->lang = $language->name;
        $preference = new Preference(['value' => $preferences]);
        $preference->user_id = 1;
        $preference->save();
    }
}
