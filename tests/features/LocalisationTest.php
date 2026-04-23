<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use LaravelEnso\Localisation\Http\Middleware\SetLanguage;
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
    public function saving_en_language_file_does_not_create_an_en_json_file()
    {
        $language = Language::query()->firstOrCreate(['name' => 'en'], [
            'display_name' => 'English',
            'flag' => 'gb',
            'is_active' => true,
            'is_rtl' => false,
        ]);
        File::delete(App::langPath('en.json'));

        $this->patch(
            route('system.localisation.saveLangFile', $language, false),
            ['langFile' => ['Hello' => 'Hello']]
        )->assertStatus(200)
            ->assertJson([
                'message' => __('The language files were successfully updated'),
            ]);

        $this->assertTrue(File::exists(App::langPath('en')));
        $this->assertFalse(File::exists(App::langPath('en.json')));
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
    public function renaming_a_language_to_en_does_not_create_an_en_json_file()
    {
        $this->post(
            route('system.localisation.store', [], false),
            $this->testModel->toArray()
        );

        $language = Language::whereName(self::LangName)->first();
        $oldName = $language->name;

        File::delete(App::langPath('en.json'));
        File::deleteDirectory(App::langPath('en'));

        $language->syncOriginal();
        $language->name = 'en';

        (new \LaravelEnso\Localisation\Observers\Language())->updated($language);

        $this->assertFalse(File::exists(App::langPath($oldName.'.json')));
        $this->assertTrue(File::exists(App::langPath('en')));
        $this->assertFalse(File::exists(App::langPath('en.json')));

        File::deleteDirectory(App::langPath('en'));
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
            'Legacy' => 'value',
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
    public function get_option_list()
    {
        $this->testModel->save();

        $this->get(route('system.localisation.options', [
            'query' => $this->testModel->name,
            'limit' => 10,
        ], false))->assertStatus(200)
            ->assertJsonFragment(['name' => $this->testModel->name]);
    }

    #[Test]
    public function can_fetch_edit_text_languages_excluding_en_and_fallback_locale()
    {
        config()->set('app.fallback_locale', 'ro');

        Language::query()->firstOrCreate(['name' => 'ro'], [
            'name' => 'ro',
            'display_name' => 'Romanian',
            'flag' => 'ro',
            'is_active' => true,
            'is_rtl' => false,
        ]);
        Language::query()->firstOrCreate(['name' => 'en'], [
            'name' => 'en',
            'display_name' => 'English',
            'flag' => 'gb',
            'is_active' => true,
            'is_rtl' => false,
        ]);
        $language = Language::query()->create([
            'name' => 'zz',
            'display_name' => 'Test German',
            'flag' => 'zz',
            'is_active' => true,
            'is_rtl' => false,
        ]);

        $response = $this->get(route('system.localisation.editTexts', [], false))
            ->assertStatus(200);

        $response->assertJsonFragment([
            'id' => $language->id,
            'name' => 'Test German',
        ])->assertJsonMissing([
            'name' => 'Romanian',
        ])->assertJsonMissing([
            'name' => 'English',
        ]);
    }

    #[Test]
    public function can_get_language_json_file_contents()
    {
        $this->testModel->save();

        File::put(App::langPath(self::LangName.'.json'), json_encode([
            'Hello' => 'Salut',
        ], JSON_FORCE_OBJECT));

        $response = $this->get(
            route('system.localisation.getLangFile', $this->testModel, false)
        )->assertStatus(200);

        $this->assertSame(['Hello' => 'Salut'], json_decode($response->getContent(), true));
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

        $this->artisan('enso:localisation:scan', ['--ignored' => 0])
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

    #[Test]
    public function scan_can_report_and_deduplicate_duplicate_translation_keys()
    {
        config()->set('enso.localisation.scan.paths', []);

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

        File::put(App::langPath($language->name.'.json'), <<<'JSON'
{
    "Same": "value",
    "Same": "value",
    "Different": "one",
    "Different": "two"
}
JSON);

        File::put(App::langPath('yy.json'), <<<'JSON'
{
    "Other Same": "value",
    "Other Same": "value"
}
JSON);

        $this->artisan('enso:localisation:scan', ['--ignored' => 0])
            ->expectsOutput('Found keys: 0')
            ->expectsOutput('New keys: 0')
            ->expectsOutput('Existing keys: 0')
            ->expectsOutput('Ignored non-literal calls: 0')
            ->expectsOutput('Duplicate keys with same translations: 2')
            ->expectsOutput('Duplicate keys with conflicting translations: 1')
            ->expectsOutput('Deduplicated duplicate keys with same translations: 2')
            ->expectsTable(
                ['Locale', 'File', 'Key', 'Translation', 'Duplicates'],
                [
                    [$language->name, App::langPath($language->name.'.json'), 'Same', 'value', 2],
                    ['yy', App::langPath('yy.json'), 'Other Same', 'value', 2],
                ]
            )->expectsTable(
                ['Locale', 'File', 'Key', 'Translations', 'Duplicates'],
                [
                    [$language->name, App::langPath($language->name.'.json'), 'Different', 'one | two', 2],
                ]
            )->assertSuccessful();

        $contents = File::get(App::langPath($language->name.'.json'));

        $this->assertSame(1, substr_count($contents, '"Same"'));
        $this->assertSame(2, substr_count($contents, '"Different"'));

        $otherContents = File::get(App::langPath('yy.json'));

        $this->assertSame(1, substr_count($otherContents, '"Other Same"'));

        $this->cleanUp($language);
        $this->cleanUp($other);
        $other->delete();
    }

    #[Test]
    public function scan_does_not_create_an_en_json_file_even_if_en_exists_as_a_language()
    {
        config()->set('app.fallback_locale', 'ro');
        config()->set('enso.localisation.scan.paths', []);

        Language::query()->firstOrCreate(['name' => 'en'], [
            'display_name' => 'English',
            'flag' => 'gb',
            'is_active' => true,
            'is_rtl' => false,
        ]);
        File::delete(App::langPath('en.json'));

        $this->artisan('enso:localisation:scan', ['--ignored' => 0])
            ->expectsOutput('Found keys: 0')
            ->expectsOutput('New keys: 0')
            ->expectsOutput('Existing keys: 0')
            ->expectsOutput('Ignored non-literal calls: 0')
            ->assertSuccessful();

        $this->assertFalse(File::exists(App::langPath('en.json')));
    }

    #[Test]
    public function publish_does_not_create_an_en_json_file_even_if_en_exists_as_a_language()
    {
        Language::query()->firstOrCreate(['name' => 'en'], [
            'display_name' => 'English',
            'flag' => 'gb',
            'is_active' => true,
            'is_rtl' => false,
        ]);
        File::delete(App::langPath('en.json'));

        $this->artisan('enso:localisation:publish', ['--locale' => 'en'])
            ->expectsOutput('Language files published (en)!')
            ->assertSuccessful();

        $this->assertTrue(File::exists(App::langPath('en')));
        $this->assertFalse(File::exists(App::langPath('en.json')));
    }

    #[Test]
    public function middleware_sets_application_locale_from_user_preferences()
    {
        $language = Language::query()->create([
            'name' => 'zz',
            'display_name' => 'Test German',
            'flag' => 'zz',
            'is_active' => true,
            'is_rtl' => false,
        ]);
        $this->user->preferences->setLanguage($language);

        $request = Request::create('/localisation-test', 'GET');
        $request->setUserResolver(fn () => $this->user->fresh());

        $response = (new SetLanguage())->handle($request, fn () => response('ok'));

        $this->assertSame('ok', $response->getContent());
        $this->assertSame('zz', App::getLocale());
    }

    #[Test]
    public function audit_duplicates_does_not_touch_en_json_file()
    {
        File::put(App::langPath('en.json'), <<<'JSON'
{
    "Same": "value",
    "Same": "value"
}
JSON);

        $original = File::get(App::langPath('en.json'));

        $result = (new \LaravelEnso\Localisation\Services\Json\AuditDuplicates(['en'], true))
            ->handle();

        $this->assertSame($original, File::get(App::langPath('en.json')));
        $this->assertCount(0, $result['same']);
        $this->assertCount(0, $result['conflicting']);
        $this->assertSame(0, $result['deduplicated']);
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
