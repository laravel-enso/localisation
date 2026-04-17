<?php

namespace LaravelEnso\Localisation\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use LaravelEnso\Localisation\Models\Language;

class LanguageSeeder extends Seeder
{
    protected array $languages = [
        ['name' => 'ro', 'display_name' => 'Romana', 'flag' => 'ro', 'is_rtl' => false, 'is_active' => true],
        ['name' => 'en', 'display_name' => 'English-GB', 'flag' => 'gb', 'is_rtl' => false, 'is_active' => true],
        ['name' => 'de', 'display_name' => 'German', 'flag' => 'de', 'is_rtl' => false, 'is_active' => true],
        ['name' => 'nl', 'display_name' => 'Nederlands', 'flag' => 'nl', 'is_rtl' => false, 'is_active' => true],
        ['name' => 'fr', 'display_name' => 'Français', 'flag' => 'fr', 'is_rtl' => false, 'is_active' => true],
        ['name' => 'br', 'display_name' => 'Brazilian Portuguese', 'flag' => 'br', 'is_rtl' => false, 'is_active' => true],
        ['name' => 'mn', 'display_name' => 'Mongolia', 'flag' => 'mn', 'is_rtl' => false, 'is_active' => true],
        ['name' => 'hu', 'display_name' => 'Magyar', 'flag' => 'hu', 'is_rtl' => false, 'is_active' => true],
        ['name' => 'es', 'display_name' => 'Español', 'flag' => 'es', 'is_rtl' => false, 'is_active' => true],
        ['name' => 'ru', 'display_name' => 'Russian', 'flag' => 'ru', 'is_rtl' => false, 'is_active' => true],
    ];

    public function run()
    {
        Collection::wrap($this->languages)
            ->each(fn ($language) => Language::factory()->create($language));
    }
}
