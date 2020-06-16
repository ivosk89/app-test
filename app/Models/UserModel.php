<?php

namespace Models;

use DB\Map\User;

class UserModel
{
    /**
     * @param string $login
     *
     * @return User
     */
    public static function getByLogin($login)
    {
        return \App::_('orm')->getRepository('DB\Map\User')->findOneBy(array('login' => $login));
    }
}