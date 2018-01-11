<?php

return [
//    'oracle' => [
//        'driver'        => 'oracle',
//        'tns'           => env('DB_TNS', ''),
//        'host'          => env('DB_HOST', '192.168.3.205'),
//        'port'          => env('DB_PORT', '1521'),
//        'database'      => env('DB_DATABASE', 'orcl'),
//        'username'      => env('DB_USERNAME', 'ncdb1'),
//        'password'      => env('DB_PASSWORD', 'ncdb1'),
//        'charset'       => env('DB_CHARSET', 'AL32UTF8'),
//        'prefix'        => env('DB_PREFIX', ''),
//        'prefix_schema' => env('DB_SCHEMA_PREFIX', ''),
//    ],
    'oracle' => [
        'driver'        => 'oracle',
        'tns'           => env('DB_TNS', ''),
        'host'          => env('DB_HOST', '192.168.3.3'),
        'port'          => env('DB_PORT', '1521'),
        'database'      => env('DB_DATABASE', 'ncdb'),
        'username'      => env('DB_USERNAME', 'tyjs56'),
        'password'      => env('DB_PASSWORD', 'tyjs56'),
        'charset'       => env('DB_CHARSET', 'AL32UTF8'),
        'prefix'        => env('DB_PREFIX', ''),
        'prefix_schema' => env('DB_SCHEMA_PREFIX', ''),
    ],
];
