<?php 

namespace LaravelEnso\Localisation\app\Console;

use Illuminate\Console\Command;
use LaravelEnso\Localisation\app\Handlers\Json\Merger;

class MergeCommand extends Command
{
    protected $signature = 'localisation:merge {--L|locale=ALL : Language key to merge (default: ALL)}';
    protected $description = 'Merges the core language files with the app language files';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $locale = $this->option('locale');
        (new Merger())->run($locale);
        $this->info('Language files merged ('.$locale.')!');
    }
}
