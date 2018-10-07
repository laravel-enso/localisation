<?php

namespace LaravelEnso\Localisation\app\Models;

use Illuminate\Database\Eloquent\Model;
use LaravelEnso\Helpers\app\Traits\ActiveState;

class Language extends Model
{
    use ActiveState;

    const FlagClassPrefix = 'flag-icon flag-icon-';

    protected $fillable = ['name', 'display_name', 'flag', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

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
