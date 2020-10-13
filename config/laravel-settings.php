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

        'wants_promotion_emails' => [
            'title' => 'Receive commercial emails',
            'description' => 'Would you like to receive commercial emails for our marketing campaign?',
            'type' => 'bool',
            'default' => true,
        ],

    ],

];
