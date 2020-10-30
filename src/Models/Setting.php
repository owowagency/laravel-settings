<?php

namespace OwowAgency\LaravelSettings\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwowAgency\LaravelSettings\Support\SettingManager;
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
        'key', 'value',
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
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable()
    {
        return config('laravel-settings.table_name');
    }

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        // Before saving the model we need to remove all keys which are added to
        // the model by the setting managers. These keys are not needed in the
        // database because they're stored in the config file.
        self::saving(function(Setting $model) {
            $unwantedKeys = array_keys(SettingManager::getMinimumConfig());

            foreach ($model->getAttributes() as $key => $attribute) {
                if (in_array($key, $unwantedKeys)) {
                    unset($model->$key);
                }
            }
        });
    }

    /**
     * Get the value converted to the configured type.
     *
     * @return mixed
     */
    public function getConvertedValueAttribute()
    {
        $configuration = SettingManager::getConfigured()->get($this->key);

        $type = data_get($configuration, 'type');

        return SettingManager::convertToType($type, $this->value);
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
