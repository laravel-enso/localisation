<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use LaravelEnso\Forms\TestTraits\CreateForm;
use LaravelEnso\Forms\TestTraits\DestroyForm;
use LaravelEnso\Forms\TestTraits\EditForm;
use LaravelEnso\Localisation\Models\Language;
use LaravelEnso\Tables\Traits\Tests\Datatable;
use LaravelEnso\Users\Models\User;
use PHPUnit\Framework\Attributes\Test;
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
    private string $originalLangPath;
    private string $testLangPath;
    private ?string $scanPath = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalLangPath = $this->app->langPath();
        $this->testLangPath = sys_get_temp_dir().'/localisation-lang-'.uniqid();

        File::makeDirectory($this->testLangPath, recursive: true);
        $this->app->useLangPath($this->testLangPath);

        $this->seed()
            ->actingAs($this->user = User::first());

        $this->testModel = Language::factory()->make([
            'name' => self::LangName,
            'flag' => self::LangName,
        ]);
    }

    protected function tearDown(): void
    {
        $this->app->useLangPath($this->originalLangPath);
        File::deleteDirectory($this->testLangPath);
        File::deleteDirectory($this->scanPath);

        parent::tearDown();
    }

    #[Test]
    public function can_store_language()
    {
        File::put(App::langPath('de.json'), json_encode([
            'Hello' => 'Hallo',
            'Bye' => 'Tschuss',
        ], JSON_FORCE_OBJECT));

        $response = $this->post(
            route('system.localisation.store'),
            $this->testModel->toArray()
        );

        $language = Language::whereName(self::LangName)->first();

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => __('The language was successfully created'),
                'redirect' => 'system.localisation.edit',
                'param' => ['language' => $language->id],
            ]);

        $this->assertTrue(
            File::exists(App::langPath($language->name))
        );

        $this->assertTrue(
            File::exists(App::langPath($language->name.'.json'))
        );

        $this->assertSame(
            [
                'Hello' => null,
                'Bye' => null,
            ],
            json_decode(File::get(App::langPath($language->name.'.json')), true)
        );

        $this->cleanUp($language);
    }

    #[Test]
    public function can_update_language()
    {
        File::put(App::langPath('de.json'), json_encode([
            'Hello' => 'Hallo',
            'Bye' => 'Tschuss',
        ], JSON_FORCE_OBJECT));

        $this->post(
            route('system.localisation.store', [], false),
            $this->testModel->toArray()
        );

        $language = Language::whereName(self::LangName)->first();
        $oldName = $language->name;

        $language->name = 'zz';

        $this->patch(
            route('system.localisation.update', $language->id, false),
            $language->toArray()
        )->assertStatus(200)
            ->assertJson([
                'message' => __('The language was successfully updated'),
            ]);

        $this->assertEquals('zz', $language->fresh()->name);

        $this->assertFalse(
            File::exists(App::langPath($oldName))
        );

        $this->assertFalse(
            File::exists(App::langPath($oldName.'.json'))
        );

        $this->assertTrue(
            File::exists(App::langPath($language->name))
        );

        $this->assertTrue(
            File::exists(App::langPath($language->name.'.json'))
        );

        $this->assertSame(
            [
                'Hello' => null,
                'Bye' => null,
            ],
            json_decode(File::get(App::langPath($language->name.'.json')), true)
        );

        $this->cleanUp($language);
    }

    #[Test]
    public function can_destroy_language()
    {
        $this->post(
            route('system.localisation.store', [], false),
            $this->testModel->toArray()
        );

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
            File::exists(App::langPath($languageName))
        );

        $this->assertFalse(
            File::exists(App::langPath($languageName.'.json'))
        );
    }

    #[Test]
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

    #[Test]
    public function cant_destroy_if_language_is_in_use()
    {
        $this->post(
            route('system.localisation.store', [], false),
            $this->testModel->toArray()
        );

        $language = Language::whereName(self::LangName)->first();
        $this->user->preferences->setLanguage($language);

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

    #[Test]
    public function can_sync_local_json_files_when_updating_a_locale()
    {
        $this->post(
            route('system.localisation.store', [], false),
            $this->testModel->toArray()
        );

        $language = Language::whereName(self::LangName)->first();
        $other = Language::factory()->create([
            'name' => 'yy',
            'display_name' => 'yy',
            'flag' => 'flag-icon flag-icon-yy',
            'is_active' => true,
            'is_rtl' => false,
        ]);

        File::put(
            App::langPath('yy.json'),
            json_encode(['Legacy' => 'value'], JSON_FORCE_OBJECT)
        );

        $this->patch(
            route('system.localisation.saveLangFile', $language, false),
            ['langFile' => ['Hello' => 'Salut', 'Bye' => 'Pa']]
        )->assertStatus(200)
            ->assertJson([
                'message' => __('The language files were successfully updated'),
            ]);

        $this->assertSame([
            'Hello' => 'Salut',
            'Bye' => 'Pa',
        ], json_decode(File::get(App::langPath($language->name.'.json')), true));

        $this->assertSame([
            'Hello' => null,
            'Bye' => null,
        ], json_decode(File::get(App::langPath('yy.json')), true));

        $this->cleanUp($language);
        $this->cleanUp($other);
        $other->delete();
    }

    #[Test]
    public function can_add_keys_to_all_local_extra_languages()
    {
        $this->post(
            route('system.localisation.store', [], false),
            $this->testModel->toArray()
        );

        $language = Language::whereName(self::LangName)->first();

        $other = Language::factory()->create([
            'name' => 'yy',
            'display_name' => 'yy',
            'flag' => 'flag-icon flag-icon-yy',
            'is_active' => true,
            'is_rtl' => false,
        ]);

        File::put(
            App::langPath($language->name.'.json'),
            json_encode(['Hello' => null], JSON_FORCE_OBJECT)
        );

        File::put(
            App::langPath('yy.json'),
            json_encode(['Hello' => null], JSON_FORCE_OBJECT)
        );

        $this->patch(
            route('system.localisation.addKey', [], false),
            ['keys' => ['World']]
        )->assertStatus(200)
            ->assertJson([
                'message' => __('The translation key was successfully added'),
            ]);

        $this->assertSame([
            'World' => null,
            'Hello' => null,
        ], json_decode(File::get(App::langPath($language->name.'.json')), true));

        $this->assertSame([
            'World' => null,
            'Hello' => null,
        ], json_decode(File::get(App::langPath('yy.json')), true));

        $this->cleanUp($language);
        $this->cleanUp($other);
        $other->delete();
    }

    #[Test]
    public function can_publish_local_language_files_for_existing_languages()
    {
        File::put(App::langPath('de.json'), json_encode([
            'Hello' => 'Hallo',
            'Bye' => 'Tschuss',
        ], JSON_FORCE_OBJECT));

        $this->post(
            route('system.localisation.store', [], false),
            $this->testModel->toArray()
        );

        $language = Language::whereName(self::LangName)->first();

        $this->cleanUp($language);

        $this->artisan('enso:localisation:publish', ['--locale' => $language->name])
            ->expectsOutput("Language files published ({$language->name})!")
            ->assertSuccessful();

        $this->assertTrue(File::exists(App::langPath($language->name)));
        $this->assertSame(
            [
                'Hello' => null,
                'Bye' => null,
            ],
            json_decode(File::get(App::langPath($language->name.'.json')), true)
        );

        $this->cleanUp($language);
    }

    #[Test]
    public function can_scan_configured_sources_and_add_unique_translation_keys()
    {
        $this->scanPath = sys_get_temp_dir().'/localisation-scan-'.uniqid();

        File::makeDirectory("{$this->scanPath}/app", recursive: true);
        File::makeDirectory("{$this->scanPath}/resources/views", recursive: true);
        File::makeDirectory("{$this->scanPath}/client/src/js", recursive: true);
        File::makeDirectory("{$this->scanPath}/client/patches", recursive: true);
        File::makeDirectory("{$this->scanPath}/client/node_modules/@enso-ui/localisation/src", recursive: true);
        File::makeDirectory("{$this->scanPath}/client/node_modules/@enso-ui/localisation/node_modules/foo", recursive: true);
        File::makeDirectory("{$this->scanPath}/vendor/laravel-enso/example/src", recursive: true);

        File::put("{$this->scanPath}/app/Test.php", "<?php __('Hello'); trans('Bye'); __('Hello');");
        File::put("{$this->scanPath}/resources/views/test.blade.php", "@lang('Blade')");
        File::put("{$this->scanPath}/client/src/js/app.js", "i18n('Frontend'); i18n(dynamicKey);");
        File::put("{$this->scanPath}/client/patches/localisation.patch", "+ i18n('Patch Key')");
        File::put("{$this->scanPath}/client/node_modules/@enso-ui/localisation/src/Test.vue", "{{ i18n('Package Key') }}");
        File::put("{$this->scanPath}/client/node_modules/@enso-ui/localisation/node_modules/foo/index.js", "i18n('Ignored Nested')");
        File::put("{$this->scanPath}/vendor/laravel-enso/example/src/Test.php", "<?php __('Vendor Key');");

        config()->set('enso.localisation.scan.paths', [
            ['path' => "{$this->scanPath}/app"],
            ['path' => "{$this->scanPath}/resources"],
            ['path' => "{$this->scanPath}/client/src/js"],
            ['path' => "{$this->scanPath}/client/patches"],
            ['path' => "{$this->scanPath}/client/node_modules/@enso-ui", 'exclude' => ['node_modules']],
            ['path' => "{$this->scanPath}/vendor/laravel-enso", 'exclude' => ['vendor']],
        ]);

        $this->post(
            route('system.localisation.store', [], false),
            $this->testModel->toArray()
        );

        $language = Language::whereName(self::LangName)->first();
        $other = Language::factory()->create([
            'name' => 'yy',
            'display_name' => 'yy',
            'flag' => 'yy',
            'is_active' => true,
            'is_rtl' => false,
        ]);

        $this->artisan('enso:localisation:scan', ['--ignored-examples' => 0])
            ->expectsOutput('Found keys: 7')
            ->expectsOutput('New keys: 7')
            ->expectsOutput('Existing keys: 0')
            ->expectsOutput('Ignored non-literal calls: 1')
            ->assertSuccessful();

        $this->assertSame([
            'Blade' => null,
            'Bye' => null,
            'Frontend' => null,
            'Hello' => null,
            'Package Key' => null,
            'Patch Key' => null,
            'Vendor Key' => null,
        ], json_decode(File::get(App::langPath($language->name.'.json')), true));

        $this->assertArrayNotHasKey(
            'Ignored Nested',
            json_decode(File::get(App::langPath($other->name.'.json')), true)
        );

        $this->cleanUp($language);
        $this->cleanUp($other);
        $other->delete();
    }

    private function cleanUp($language): void
    {
        File::delete(
            App::langPath($language->name.'.json')
        );

        File::deleteDirectory(
            App::langPath($language->name)
        );
    }
}
