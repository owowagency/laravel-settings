<?php

return [
    /**
     * The JSON resources to be used in JSON responses.
     */
    'resources' => [
        'setting' => '\OwowAgency\LaravelSettings\Resources\SettingResource',
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
