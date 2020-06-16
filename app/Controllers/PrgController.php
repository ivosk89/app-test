<?php

namespace Controllers;

use Models\UserModel;
use Silex\Application;
use Silex\Provider;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;

class PrgController
{

    public function index()
    {
        return new Response();
    }

    public function dump()
    {
        \App::_('mysqlkeep')->TableDumper();
        return new Response();
    }

    public function update()
    {
        \App::_('mysqlkeep')->TableUpdater();
        return new Response();
    }

    public function map()
    {
        // Change directory to project root
        $cd = 'cd ' . DIR_ROOT;
        //Generate entities;
        $entity = 'php vendor/doctrine/orm/bin/doctrine.php orm:convert-mapping --namespace="DB\\Map\\\" --force  --from-database annotation ' . DIR_APP;
        //Generate setters & getters
        $gs = 'php vendor/doctrine/orm/bin/doctrine.php orm:generate-entities ' . DIR_APP . ' --generate-annotations=true';
        //Generate proxies
        $proxy = 'php vendor/doctrine/orm/bin/doctrine.php orm:generate-proxies';

        $out = shell_exec($cd . ' && ' . $entity . ' && ' . $gs . ' && ' . $proxy);

        return new Response("<pre>" . $out . "</pre>");
    }

}