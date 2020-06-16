<?php

class App
{
    /**
     * @var Application
     */
    private static $app = null;

    private function __construct(Silex\Application $application)
    {
        self::$app = $application;
    }

    public static function create(Silex\Application $application)
    {
        if (!self::$app) {
            new self($application);
        }
        return self::$app;
    }

    public static function _($key = null)
    {
        if (!self::$app) {
            throw new \Exception("Application is not created");
        }
        if (!isset($key)) {
            return self::$app;
        }
        return self::$app[$key];
    }

    public static function get($key)
    {
        return self::_($key);
    }

    public static function set($key, $value)
    {
        if (!self::$app) {
            throw new \Exception("Application is not created");
        }
        self::$app[$key] = $value;
        return $value;
    }
}