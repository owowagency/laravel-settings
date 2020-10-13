<?php

namespace OwowAgency\LaravelSettings\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwowAgency\LaravelSettings\Tests\Support\Database\Factories\SettingFactory;

class Setting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'group', 'settings',
    ];
    
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'settings' => 'array',
    ];
    
    /**
     * Get the model instance that the setting belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to only include settings that are in the specified group(s).
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int|int[]  $groups
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInGroup(Builder $query, ...$groups): Builder
    {
        return $query->whereIn($this->getTable() . '.group', $groups);
    }

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable()
    {
        return config('laravel-settings.table_name');
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return SettingFactory::new();
    }
}
