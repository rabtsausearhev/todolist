<?php

namespace App\Controllers;

use App\Models\UsersModel;

class UsersController extends BaseController
{
    public static function login()
    {
        $username = $_REQUEST['username'];
        $password = $_REQUEST['password'];
        $code = -1;
        $cookies = null;
        $message = 'The username or password you entered is incorrect';
        $usersModel = new UsersModel();
        $user = $usersModel->checkUserForLogin($username, $password);
        if ($user) {
            $cookies = hash ( 'md5' , $user['username'] . $user['password'], false ) ;
            $code = 0;
            $message = 'admin';
            $usersModel->setCookies($username, $cookies);
        }
        echo json_encode(['code' => $code, 'message' => $message, 'cookies'=>$cookies]);
    }
    public static function logout(){
        $cookies =  $_COOKIE['todoUser'];
        $usersModel = new UsersModel();
        $usersModel->removeCookiesForLogout($cookies);
        echo json_encode(['code' => 0]);
    }
}
