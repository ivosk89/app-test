<?php
/*
 * Config for proper usage Doctrine ORM CLI
 */
/*
 *
 * Command examples
 *
 * Generate entity classes (app/DB/Map/):
 * php vendor/doctrine/orm/bin/doctrine.php orm:convert-mapping --namespace="DB\\Map\\" --force  --from-database annotation app/ --filter="TableName"
 *
 * Generate setters/getters (in app/DB/Map/):
 * php vendor/doctrine/orm/bin/doctrine.php orm:generate-entities app/ --generate-annotations=true
 *
 * Generate proxies (in app/cache/orm/):
 * php vendor/doctrine/orm/bin/doctrine.php orm:generate-proxies
 *
 */
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Symfony\Component\Console\Helper\HelperSet;

require_once __DIR__ . '/vendor/autoload.php';
$app = \App::create(new \Application());
require_once __DIR__ . '/app/config/config.php';
$app->init();

$helperSet = new HelperSet(array(
    'db' => new ConnectionHelper($app['orm']->getConnection()),
    'em' => new EntityManagerHelper($app['orm'])
));

ConsoleRunner::run($helperSet);