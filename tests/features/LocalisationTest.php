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
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

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

    #[Test]
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
            File::exists(App::langPath($language->name))
        );

        $this->assertTrue(
            File::exists(App::langPath($language->name.'.json'))
        );

        $this->cleanUp($language);
    }

    #[Test]
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

    #[Test]
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
            $this->testModel->toArray() + [
                'flag_sufix' => self::LangName,
            ]
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
    public function can_sync_app_language_files_when_updating_a_locale()
    {
        $source = $this->createLanguage('xx');
        $target = $this->createLanguage('yy');

        $this->writeJsonFile($this->appJsonPath($source->name), [
            'alpha' => 'Source alpha',
            'beta' => 'Source beta',
        ]);

        $this->writeJsonFile($this->appJsonPath($target->name), [
            'alpha' => 'Target alpha',
            'obsolete' => 'Remove me',
        ]);

        $this->patch(
            route('system.localisation.saveLangFile', [
                'language' => $source->id,
                'subDir' => 'app',
            ], false),
            ['langFile' => $this->readJson($this->appJsonPath($source->name))]
        )->assertStatus(200)
            ->assertJson([
                'message' => __('The language files were successfully updated'),
            ]);

        $updated = $this->readJson($this->appJsonPath($target->name));

        $this->assertSame('Target alpha', $updated['alpha']);
        $this->assertArrayHasKey('beta', $updated);
        $this->assertNull($updated['beta']);
        $this->assertArrayNotHasKey('obsolete', $updated);

        $this->cleanUp($source);
        $this->cleanUp($target);
    }

    #[Test]
    public function updating_app_language_files_does_not_touch_core_language_files()
    {
        $source = $this->createLanguage('xx');
        $target = $this->createLanguage('yy');

        $this->writeJsonFile($this->appJsonPath($source->name), [
            'alpha' => 'Source alpha',
        ]);

        $coreTranslations = [
            'core_only' => 'Core value',
        ];

        $this->writeJsonFile($this->coreJsonPath($target->name), $coreTranslations);

        $this->patch(
            route('system.localisation.saveLangFile', [
                'language' => $source->id,
                'subDir' => 'app',
            ], false),
            ['langFile' => $this->readJson($this->appJsonPath($source->name))]
        )->assertStatus(200);

        $this->assertSame($coreTranslations, $this->readJson($this->coreJsonPath($target->name)));

        $this->cleanUp($source);
        $this->cleanUp($target);
    }

    #[Test]
    public function can_add_keys_to_all_extra_languages_in_the_configured_directory()
    {
        $first = $this->createLanguage('xx');
        $second = $this->createLanguage('yy');

        $this->writeJsonFile($this->appJsonPath($first->name), []);
        $this->writeJsonFile($this->appJsonPath($second->name), []);
        $this->writeJsonFile($this->coreJsonPath($first->name), []);
        $this->writeJsonFile($this->coreJsonPath($second->name), []);

        $this->patch(route('system.localisation.addKey', [], false), [
            'keys' => ['fresh.key'],
        ])->assertStatus(200)
            ->assertJson([
                'message' => __('The translation key was successfully added'),
            ]);

        $this->assertArrayHasKey('fresh.key', $this->readJson($this->appJsonPath($first->name)));
        $this->assertArrayHasKey('fresh.key', $this->readJson($this->appJsonPath($second->name)));
        $this->assertArrayNotHasKey('fresh.key', $this->readJson($this->coreJsonPath($first->name)));
        $this->assertArrayNotHasKey('fresh.key', $this->readJson($this->coreJsonPath($second->name)));

        $this->cleanUp($first);
        $this->cleanUp($second);
    }

    #[Test]
    public function can_merge_language_files_and_remove_duplicate_app_keys()
    {
        $language = $this->createLanguage('xx');

        $this->writeJsonFile($this->coreJsonPath($language->name), [
            'shared' => 'Core value',
            'core_only' => 'Core only',
        ]);

        $this->writeJsonFile($this->appJsonPath($language->name), [
            'shared' => 'App value',
            'app_only' => 'App only',
        ]);

        $this->patch(route('system.localisation.merge', [
            'locale' => $language->name,
        ], false))->assertStatus(200)
            ->assertJson([
                'message' => __('The language files were successfully merged'),
            ]);

        $app = $this->readJson($this->appJsonPath($language->name));
        $merged = $this->readJson($this->mergedJsonPath($language->name));

        $this->assertArrayNotHasKey('shared', $app);
        $this->assertSame('App only', $app['app_only']);
        $this->assertSame('Core value', $merged['shared']);
        $this->assertSame('Core only', $merged['core_only']);
        $this->assertSame('App only', $merged['app_only']);

        $this->cleanUp($language);
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

    private function createLanguage(string $name): Language
    {
        $this->cleanupLocaleFiles($name);

        $language = Language::factory()->make([
            'name' => $name,
            'display_name' => "lang-{$name}",
            'flag' => 'flag-icon flag-icon-'.$name,
            'is_rtl' => false,
            'is_active' => true,
        ]);

        $this->post(
            route('system.localisation.store', [], false),
            $language->toArray() + ['flag_sufix' => $name]
        )->assertStatus(200);

        return Language::whereName($name)->firstOrFail();
    }

    private function cleanupLocaleFiles(string $locale): void
    {
        File::delete(App::langPath("{$locale}.json"));
        File::delete($this->appJsonPath($locale));
        File::delete($this->coreJsonPath($locale));
        File::deleteDirectory(App::langPath($locale));
    }

    private function appJsonPath(string $locale): string
    {
        return App::langPath('app'.DIRECTORY_SEPARATOR."{$locale}.json");
    }

    private function coreJsonPath(string $locale): string
    {
        return base_path("vendor/laravel-enso/localisation/lang/enso/{$locale}.json");
    }

    private function mergedJsonPath(string $locale): string
    {
        return App::langPath("{$locale}.json");
    }

    private function writeJsonFile(string $path, array $contents): void
    {
        File::put(
            $path,
            json_encode($contents, JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        );
    }

    private function readJson(string $path): array
    {
        return json_decode(File::get($path), true);
    }
}
