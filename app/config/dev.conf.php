<?php
$app['debug'] = true;
return array(
    'db' => array(
        'db.options' => array(
            'host' => 'localhost',
            'dbname' => 'test',
            'user' => 'root',
            'password' => ''
        )
    ),
    'http_cache' => array(
        'http_cache.options' => array(
            'allow_revalidate' => true
        )
    ),
    'security' => array(
        'security.firewalls' => array(
            'prg' => array(
                'users' => array(
                    'admin' => array('ROLE_ADMIN', 'BFEQkknI/c+Nd7BaG7AaiyTfUFby/pkMHy3UsYqKqDcmvHoPRX/ame9TnVuOV2GrBH0JK9g4koW+CgTYI9mK+w=='),
                ),
            )
        ),
    ),
    'twig' => [
        'twig.options' => [
            'cache' => false
        ],
    ],
);