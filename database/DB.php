<?php

declare(strict_types=1);
class DB
{
    private const HOST = "localhost";
    private const DB_NAME = "WarehouseDB";
    private const DB_USERNAME = "root";
    private const DB_PASSWORD = "";

    private static ?PDO $db = null;

    private function __construct(){}

    public static function getInstance()
    {
        if(!self::$db)
        {
            try
            {
                self::$db = new PDO('mysql:host='.self::HOST.';dbname='.self::DB_NAME,self::DB_USERNAME,self::DB_PASSWORD);
            }
            catch(PDOException $e)
            {
                echo "Connection failed: ".$e->getMessage();
                exit;
            }
        }
        return self::$db;
    }

    public static function disconnectIfConnected()
    {
        if(self::$db)
        {
            self::$db=null;
        }
    }
}