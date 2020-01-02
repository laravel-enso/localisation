<?php

namespace LaravelEnso\Localisation\App\Models;

use Illuminate\Database\Eloquent\Model;
use LaravelEnso\Helpers\App\Contracts\Activatable;
use LaravelEnso\Helpers\App\Traits\ActiveState;
use LaravelEnso\Tables\App\Traits\TableCache;

class Language extends Model implements Activatable
{
    use ActiveState, TableCache;

    public const FlagClassPrefix = 'flag-icon flag-icon-';

    protected $fillable = ['name', 'display_name', 'flag', 'is_rtl', 'is_active'];

    protected $casts = ['is_rtl' => 'boolean', 'is_active' => 'boolean'];

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
