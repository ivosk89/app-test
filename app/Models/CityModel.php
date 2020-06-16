<?php

namespace Models;

use DB\Map\City;
use DB\Map\Region;

class CityModel
{

    /**
     * @return City[]
     */
    public static function getAll()
    {
        return \App::_('orm')->getRepository('DB\Map\City')->findAll();
    }

    /**
     * @param int|Region $region
     *
     * @return City[]
     */
    public static function getByRegion($region)
    {
        return \App::_('orm')->getRepository('DB\Map\City')->findBy(['region' => $region]);
    }

}