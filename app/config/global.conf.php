<?php
use Monolog\Logger;

return [
    'db' => [
        'db.options' => [
            'driver' => 'pdo_mysql',
            'host' => 'localhost',
            'dbname' => 'test',
            'user' => 'root',
            'password' => ''
        ]
    ],
    'mysqlkeep' => [
        'mysqlkeep.sqlpath' => DIR_DB_KEEP
    ],
    'http_cache' => [
        'http_cache.cache_dir' => DIR_CASHE . 'http',
        'http_cache.options' => [
            'debug' => $app['debug'],
            'default_ttl' => 300,
            'private_headers' => ['Authorization', 'Cookie'],
            'allow_reload' => true,
            'allow_revalidate' => false
        ]
    ],
    'security' => [
        'security.firewalls' => [
            'prg' => [
                'pattern' => '^/prg',
                'http' => true,
                'users' => [
                    'admin' => ['ROLE_ADMIN', 'BFEQkknI/c+Nd7BaG7AaiyTfUFby/pkMHy3UsYqKqDcmvHoPRX/ame9TnVuOV2GrBH0JK9g4koW+CgTYI9mK+w=='],
                ],
            ]
        ]
    ],
    'twig' => [
        'twig.options' => [
            'cache' => DIR_CASHE . 'twig',
            'strict_variables' => true,
            'debug' => $app['debug'],
            'autoescape' => true,
            'auto_reload' => !$app['debug']
        ],
        'twig.path' => DIR_VIEW,
    ],
    'monolog' => [
        'monolog.logfile' => DIR_APP . 'log' . DS . 'app.log',
        'monolog.name' => 'app',
        'monolog.level' => $app['debug'] ? Logger::DEBUG : Logger::WARNING
    ]
];