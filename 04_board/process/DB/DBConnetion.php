<?php

class DbConnection
{
    public static function connect()
    {
        require_once(__DIR__ . "/../../vendor/autoload.php");
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/../../");
        $dotenv->load();

        $db_name = $_ENV['DB_NAME'];
        $db_user = $_ENV['DB_USER'];
        $db_password = $_ENV['DB_PASSWORD'];

        $dsn = 'mysql:dbname=' . $db_name . ';host=localhost';

        try {
            $dbh = new PDO($dsn, $db_user, $db_password);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        } catch (PDOException $e) {
            print('Error:' . $e->getMessage());
            die();
        }

        return $dbh;
    }
}
