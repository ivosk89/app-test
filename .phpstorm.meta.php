<?php
namespace PHPSTORM_META {

    /** @noinspection PhpUnusedLocalVariableInspection */
    /** @noinspection PhpIllegalArrayKeyTypeInspection */
    /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
    $STATIC_METHOD_TYPES = [
        \App::_('') => [
            'db' instanceof \Doctrine\DBAL\Connection,
            'http_cache' instanceof \Silex\HttpCache,
            'twig' instanceof \Twig_Environment,
            'routes' instanceof \Symfony\Component\Routing\RouteCollection,
            'mysqlkeep' instanceof \Services\MySQLKeeper,
            'orm' instanceof \Doctrine\ORM\EntityManager,
            'form.factory' instanceof \Symfony\Component\Form\FormFactory,
            'imagine' instanceof \Imagine\Imagick\Imagine,
            'logger' instanceof \Monolog\Logger,
            'session' instanceof \Symfony\Component\HttpFoundation\Session\Session,
            'security' instanceof \Symfony\Component\Security\Core\SecurityContextInterface,
	        'security.encoder_factory' instanceof \Symfony\Component\Security\Core\Encoder\EncoderFactory,
            'security.encoder.digest' instanceof \Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder,
            'validator' instanceof \Symfony\Component\Validator\Validator,
            'url_generator' instanceof \Symfony\Component\Routing\Generator\UrlGenerator
        ],
        \App::get('') => [
            'db' instanceof \Doctrine\DBAL\Connection,
            'http_cache' instanceof \Silex\HttpCache,
            'twig' instanceof \Twig_Environment,
            'routes' instanceof \Symfony\Component\Routing\RouteCollection,
            'mysqlkeep' instanceof \Services\MySQLKeeper,
            'orm' instanceof \Doctrine\ORM\EntityManager,
            'form.factory' instanceof \Symfony\Component\Form\FormFactory,
            'imagine' instanceof \Imagine\Imagick\Imagine,
            'logger' instanceof \Monolog\Logger,
            'session' instanceof \Symfony\Component\HttpFoundation\Session\Session,
            'security' instanceof \Symfony\Component\Security\Core\SecurityContextInterface,
	        'security.encoder_factory' instanceof \Symfony\Component\Security\Core\Encoder\EncoderFactory,
            'security.encoder.digest' instanceof \Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder,
            'validator' instanceof \Symfony\Component\Validator\Validator,
            'url_generator' instanceof \Symfony\Component\Routing\Generator\UrlGenerator
        ],
    ];

}