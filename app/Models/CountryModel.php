<?php

namespace Models;

use DB\Map\Country;

class CountryModel
{

    /**
     * @return Country[]
     */
    public static function getAll()
    {
        return \App::_('orm')->getRepository('DB\Map\Country')->findAll();
    }

}