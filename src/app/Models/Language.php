<?php

namespace LaravelEnso\Localisation\app\Models;

use Illuminate\Database\Eloquent\Model;
use LaravelEnso\Helpers\app\Traits\ActiveState;
use LaravelEnso\VueDatatable\app\Traits\TableCache;
use LaravelEnso\Multitenancy\app\Traits\SystemConnection;

class Language extends Model
{
    use ActiveState, SystemConnection, TableCache;

    const FlagClassPrefix = 'flag-icon flag-icon-';

    protected $fillable = ['name', 'display_name', 'flag', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    protected $cachedTable = 'localisation';

    public function updateWithFlagSufix($attributes, string $sufix)
    {
        $this->fill($attributes);
        $this->flag = self::FlagClassPrefix.$sufix;
        $this->update();
    }

    public function storeWithFlagSufix($attributes, string $sufix)
    {
        $this->fill($attributes);
        $this->flag = self::FlagClassPrefix.$sufix;

        return tap($this)->save();
    }

    public function scopeExtra($query)
    {
        return $query->where('name', '<>', config('app.fallback_locale'));
    }
}
