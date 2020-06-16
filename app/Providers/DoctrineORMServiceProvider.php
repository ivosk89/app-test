<?php

namespace Providers;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Doctrine\ORM\Tools\Setup;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\Console\Helper\HelperSet;

class DoctrineORMServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['orm'] = $app->share(function ($app) {
            $cache = new \Doctrine\Common\Cache\ArrayCache();
            $driver = new \Doctrine\ORM\Mapping\Driver\AnnotationDriver(new AnnotationReader(), array(DIR_APP . 'DB' . DS . 'Map'));
            $config = Setup::createAnnotationMetadataConfiguration(array(DIR_APP . 'DB' . DS . 'Map'), $app['debug']);
            $config->setMetadataCacheImpl($cache);
            $config->setQueryCacheImpl($cache);
            $config->setMetadataDriverImpl($driver);
            $config->setProxyDir(DIR_APP . 'cache' . DS . "orm");
            $config->setProxyNamespace("DB\\Map");
            $config->setAutoGenerateProxyClasses(TRUE);
            $em = EntityManager::create($app['db.options'], $config);
            $app['orm.helper'] = new HelperSet(array(
                'db' => new ConnectionHelper($em->getConnection()),
                'em' => new EntityManagerHelper($em)
            ));

            return $em;
        });
        // support the Mysql enum type
        \App::_('orm')->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
    }

    public function boot(Application $app)
    {
    }
}
