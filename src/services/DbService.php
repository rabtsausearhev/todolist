<?php

namespace App\Services;

use App\Exceptions\SecurityException;
use PDO;

class DbService
{
    const DB_HOST = 'DB_HOST';
    const DB_USERNAME = 'DB_USERNAME';
    const DB_PASSWORD = 'DB_PASSWORD';
    const DB_DATABASE = 'DB_DATABASE';

    private static $connection;

    public static function getConnection(){
        if(!self::$connection){
            $securityJson = file_get_contents('../security.json');
            if (!$securityJson) throw new SecurityException('Check for a security settings file');
            $security = json_decode($securityJson, true);
            if (!array_key_exists (self::DB_HOST, $security) || !array_key_exists (self::DB_USERNAME, $security) || !array_key_exists (self::DB_PASSWORD, $security) || !array_key_exists (self::DB_DATABASE, $security)) {
                throw new SecurityException('Check the correctness of the secret data');
            }
            $db_host = $security[self::DB_HOST];
            $db_database = $security[self::DB_DATABASE];
            $db_username = $security[self::DB_USERNAME];
            $db_password = $security[self::DB_PASSWORD];
            self::$connection = new PDO('mysql:host='.$db_host.';dbname=' . $db_database, $db_username, $db_password);
        }
        return self::$connection;
    }
}
