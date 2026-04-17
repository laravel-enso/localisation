<?php

namespace LaravelEnso\Localisation\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use LaravelEnso\Helpers\Contracts\Activatable;
use LaravelEnso\Helpers\Traits\ActiveState;
use LaravelEnso\Tables\Traits\TableCache;

class Language extends Model implements Activatable
{
    use ActiveState, HasFactory, TableCache;

    public const FlagPrefix = 'flag-icon flag-icon-';

    protected $guarded = ['id'];

    public function scopeExtra($query)
    {
        return $query->where('name', '<>', config('app.fallback_locale'));
    }

    protected function casts(): array
    {
        return [
            'is_rtl' => 'boolean', 'is_active' => 'boolean',
        ];
    }

    public function flag(): string
    {
        return self::FlagPrefix.$this->flag;
    }
}
