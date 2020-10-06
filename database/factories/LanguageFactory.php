<?php

namespace LaravelEnso\Localisation\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use LaravelEnso\Localisation\Models\Language;

class LanguageFactory extends Factory
{
    protected $model = Language::class;

    public function definition()
    {
        $countryCode = $this->faker->countryCode;

        return [
            'name'         => $countryCode,
            'display_name' => strtolower($this->faker->country),
            'flag'         => 'flag-icon flag-icon-'.$countryCode,
            'is_rtl'       => $this->faker->boolean,
            'is_active'    => $this->faker->boolean,
        ];
    }
}
