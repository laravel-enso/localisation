<?php

namespace LaravelEnso\Localisation\app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use LaravelEnso\Localisation\app\Models\Language;
use LaravelEnso\Localisation\app\Classes\Json\Updater;

class AddKey implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $data;
    
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function handle()
    {
        (new Updater(new Language, $this->data))
            ->addKey();
    }
}
