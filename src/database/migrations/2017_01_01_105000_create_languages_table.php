<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use LaravelEnso\Localisation\app\Models\Language;

class CreateLanguagesTable extends Migration
{
    public function up()
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('display_name')->unique();
            $table->string('flag')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $languages = [
            ['name' => 'ro', 'display_name' => 'Romana', 'flag' => 'flag-icon flag-icon-ro', 'is_active' => true],
            ['name' => 'en', 'display_name' => 'English-GB', 'flag' => 'flag-icon flag-icon-gb', 'is_active' => true],
            ['name' => 'de', 'display_name' => 'German', 'flag' => 'flag-icon flag-icon-de', 'is_active' => true],
            ['name' => 'nl', 'display_name' => 'Nederlands', 'flag' => 'flag-icon flag-icon-nl', 'is_active' => true],
            ['name' => 'fr', 'display_name' => 'Français', 'flag' => 'flag-icon flag-icon-fr', 'is_active' => true],
            ['name' => 'br', 'display_name' => 'Brazilian Portuguese', 'flag' => 'flag-icon flag-icon-br', 'is_active' => true],
            ['name' => 'ar', 'display_name' => 'Arabic', 'flag' => 'flag-icon flag-icon-eg', 'is_active' => true],
            ['name' => 'mn', 'display_name' => 'Mongolia', 'flag' => 'flag-icon flag-icon-mn', 'is_active' => true],
            ['name' => 'hu', 'display_name' => 'Magyar', 'flag' => 'flag-icon flag-icon-hu', 'is_active' => true],
        ];

        \DB::transaction(function () use ($languages) {
            foreach ($languages as $language) {
                Language::create($language);
            }
        });
    }

    public function down()
    {
        Schema::dropIfExists('languages');
    }
}
