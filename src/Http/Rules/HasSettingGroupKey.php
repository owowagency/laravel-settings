<?php

namespace OwowAgency\LaravelSettings\Http\Rules;

use OwowAgency\LaravelSettings\Support\SettingManager;

class HasSettingGroupKey extends BaseSettingRule
{
    /**
     * Validates if value is a base64 string and the correct mime type.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return boolean
     */
    public function passes($attribute, $value)
    {
        return SettingManager::groupExists($value);
    }

    /**
     * Get the validation message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.exists');
    }
}
