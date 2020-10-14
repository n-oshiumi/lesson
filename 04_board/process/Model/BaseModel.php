<?php
require __DIR__ . "/../DB/DBConnetion.php";

class BaseModel
{
    protected $dbh;

    public function __construct()
    {
        $dbh = DbConnection::connect();
        $this->dbh = $dbh;
    }

    protected function connect()
    {
        return DbConnection::connect();
    }
}
