<?php


namespace App\Controllers;


use App\Models\UsersModel;

class BaseController
{
    protected static function userVerification()
    {
        $cookie = $_COOKIE['todoUser'];
        if ($cookie) {
            $usersModel = new UsersModel();
            $user = $usersModel->getUserByCookies($cookie);
            if ($user) {
                return true;
            }
        }
        return false;
    }
}
