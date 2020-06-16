<?php
use Symfony\Component\HttpFoundation\Request;

require_once __DIR__ . '/../vendor/autoload.php';
$app = \App::create(new \Application());
require_once __DIR__ . '/../app/config/config.php';
$app->init();
$app->route();

Request::setTrustedProxies(array('127.0.0.1'));
\App::_('http_cache')->run();
