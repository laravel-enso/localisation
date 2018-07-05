<?php

namespace LaravelEnso\Localisation\app\Commands;

use Illuminate\Console\Command;
use LaravelEnso\Localisation\app\Classes\Json\Merger;

class MergeCommand extends Command
{
    protected $signature = 'localisation:merge {--L|locale= : Language key to merge (default: all)}';
    protected $description = 'Merges the core language files with the app language files';

    public function handle()
    {
        $locale = $this->option('locale');

        (new Merger())->run($locale);

        $this->info('Language files merged ('.($locale ?: 'all').')!');
    }
}
