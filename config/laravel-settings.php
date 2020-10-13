<?php

return [

    /**
     * The table name for the settings table.
     */
    'table_name' => 'settings',

    /**
     * The JSON resources to be used in JSON responses.
     */
    'resources' => [
        'setting' => \OwowAgency\LaravelSettings\Resources\SettingResource::class,
    ],

    'settings' => [
        'follow_notification' => [
            'title' => 'Yo',
            'description' => '',
            'type' => 'bool',
            'default' => true,
        ],
    ],

];
