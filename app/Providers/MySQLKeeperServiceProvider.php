<?php
namespace Providers;

use Services\MySQLKeeper;
use Silex\Application;
use Silex\ServiceProviderInterface;

class MySQLKeeperServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['mysqlkeep'] = $app->share(function ($app) {
            return new MySQLKeeper($app);
        });
    }

    public function boot(Application $app)
    {

    }
}

