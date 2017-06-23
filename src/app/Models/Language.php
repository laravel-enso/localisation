<?php

namespace LaravelEnso\Localisation\app\Models;

use Illuminate\Database\Eloquent\Model;
use LaravelEnso\Helpers\Traits\FormattedTimestamps;

class Language extends Model
{
    use FormattedTimestamps;

    protected $fillable = ['name', 'display_name', 'flag'];

    public static function scopeExtra($query)
    {
        return $query->where('name', '<>', config('app.fallback_locale'));
    }
}
