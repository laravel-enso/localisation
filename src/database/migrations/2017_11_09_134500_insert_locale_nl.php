<?php

use Illuminate\Database\Migrations\Migration;
use LaravelEnso\Localisation\app\Models\Language;

class InsertLocaleNl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Language::firstOrCreate(['name' => 'nl'],
                    ['display_name' => 'Nederlands', 'flag' => 'flag-icon flag-icon-nl']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Language::where('name', 'nl')->delete();
    }
}
