<?php

namespace App\Models;

use App\Services\DbService;
use PDO;

class UsersModel
{
    public function checkUserForLogin($username, $password){
        $db = DbService::getConnection();
        $sql = "select * from users where username = :username and password = :password";
        $stmt = $db->prepare($sql);
        $stmt->bindParam('username', $username, PDO::PARAM_STR);
        $stmt->bindParam('password', $password, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function setCookies($username, $cookies){
        $db = DbService::getConnection();
        $sql = "update users set cookies = :cookies where username = :username";
        $stmt = $db->prepare($sql);
        $stmt->bindParam('cookies', $cookies, PDO::PARAM_STR);
        $stmt->bindParam('username', $username, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function removeCookiesForLogout($cookies){
        $db = DbService::getConnection();
        $sql = "update users set cookies = '' where cookies = :cookies";
        $stmt = $db->prepare($sql);
        $stmt->bindParam('cookies', $cookies, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getUserByCookies($cookies){
        $db = DbService::getConnection();
        $sql = "select * from users where cookies = :cookies";
        $stmt = $db->prepare($sql);
        $stmt->bindParam('cookies', $cookies, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch();
    }
}
