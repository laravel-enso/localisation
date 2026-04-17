<?php

namespace LaravelEnso\Localisation\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use LaravelEnso\Localisation\Models\Language;
use LaravelEnso\Localisation\Services\Json\SaveToDisk;
use LaravelEnso\Localisation\Services\Json\Template;
use LaravelEnso\Localisation\Services\Legacy\Store;

class Publish extends Command
{
    protected $signature = 'enso:localisation:publish {--locale=}';

    protected $description = 'Publishes the local language folders and empty JSON files';

    public function handle(): int
    {
        if (! Schema::hasTable('languages')) {
            $this->info("Couldn't publish the localisation files before migrating the database.");

            return self::SUCCESS;
        }

        Language::query()
            ->when($this->option('locale'), fn ($query, $locale) => $query
                ->whereName($locale))->pluck('name')
            ->each(function (string $locale): void {
                (new Store($locale))->handle();
                SaveToDisk::handle($locale, Template::handle($locale));
            });

        $locale = $this->option('locale') ?? 'all';

        $this->info("Language files published ({$locale})!");

        return self::SUCCESS;
    }
}
