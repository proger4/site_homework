<?php

return [
    'components' => [
        'db' => [
            'connectionString' => 'sqlite:' . dirname(__DIR__, 3) . '/database/app.sqlite',
            'username' => null,
            'password' => null,
        ],
    ],
];
