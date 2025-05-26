<?php

return [
    'default' => 'admin',
    'panels' => [
        'admin' => [
            'path' => 'admin',
            'brandName' => 'Panel de Administración',
            'auth' => [
                'guard' => 'web',
                'middleware' => ['auth'],
            ],
        ],
        'alumno' => [
            'path' => 'alumno',
            'brandName' => 'Panel de Alumnos',
            'auth' => [
                'guard' => 'web',
                'middleware' => ['auth'],
            ],
        ],
    ],
    'resources' => [
        'namespace' => 'App\\Filament\\Resources',
    ],
    'pages' => [
        'namespace' => 'App\\Filament\\Pages',
    ],
];
