<?php
$app['debug'] = false;

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
            //TODO: Fix permanent revalidation issue
            'allow_revalidate' => false
        )
    ),
    'security' => array(
        'security.firewalls' => array(
            'prg' => array(
                'users' => array(
                    //TODO: change production password
                    'admin' => array('ROLE_ADMIN', 'BFEQkknI/c+Nd7BaG7AaiyTfUFby/pkMHy3UsYqKqDcmvHoPRX/ame9TnVuOV2GrBH0JK9g4koW+CgTYI9mK+w=='),
                ),
            )
        ),
    )
);