<?php

namespace LaravelEnso\Localisation;;

use Illuminate\Database\Eloquent\Model;

/**
 *
 * @property integer $id
 * @property string $name
 * @property string $display_name
 * @property string $flag
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Language whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Language whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Language whereDisplayName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Language whereFlag($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Language whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Language whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Language extends Model
{

    protected $fillable = ['name', 'display_name', 'flag'];

    public static function allExceptDefault()
    {
    	return Language::where('name', '<>', config('app.fallback_locale'));
    }
}
