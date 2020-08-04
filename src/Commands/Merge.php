<?php

namespace LaravelEnso\Localisation\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use LaravelEnso\Localisation\Services\Json\Merger;

class Merge extends Command
{
    protected $signature = 'enso:localisation:merge {--locale=}';

    protected $description = 'Merges the core language files with the app language files';

    public function handle()
    {
        $locale = $this->option('locale');

        if (!Schema::hasTable('languages')) {
            $this->info("Couldn't merge the localisation files before migrating the database.");

            return;
        }

        (new Merger())->run($locale);

        $locale ??= 'all';

        $this->info("Language files merged ({$locale})!");
    }
}
