<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use LaravelEnso\Localisation\app\Models\Language;

class CreateLanguagesTable extends Migration
{
    public function up()
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('display_name');
            $table->string('flag');
            $table->timestamps();
        });

        $languages = [
            ['name' => 'ro', 'display_name' => 'Romana', 'flag' => 'flag-icon-ro'],
            ['name' => 'en', 'display_name' => 'English-GB', 'flag' => 'flag-icon-gb'],
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
