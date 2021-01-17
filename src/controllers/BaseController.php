<?php


namespace App\Controllers;


use App\Models\UsersModel;

/**
 * Class BaseController
 * @package App\Controllers
 *
 * created as a repository of functionality common to controllers
 */
class BaseController
{
    /**
     * @return bool
     *
     * checks if a user is in the database
     */
    protected static function userVerification(): bool
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
