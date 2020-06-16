<?php

namespace Models;

use DB\Map\Region;
use DB\Map\Country;

class RegionModel
{

    /**
     * @return Region[]
     */
    public static function getAll()
    {
        return \App::_('orm')->getRepository('DB\Map\Region')->findAll();
    }

    /**
     * @param int|Country $country
     *
     * @return Region[]
     */
    public static function getByCountry($country)
    {
        return \App::_('orm')->getRepository('DB\Map\Region')->findBy(['country' => $country]);
    }
}