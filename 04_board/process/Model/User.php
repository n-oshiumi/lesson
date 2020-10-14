<?php

require_once __DIR__ . "/BaseModel.php";

class User extends BaseModel
{
    private $tableName = 'users';

    public function checEmailExists($email)
    {
        return $this->dbh->query('SELECT * FROM users WHERE email=' . $email);
    }

    public function create($name, $email, $password, $thumbnail_url)
    {
        var_dump($thumbnail_url);
        $stmt = $this->dbh->prepare("insert into users(name,email,thumbnail_url,password,created_at) values(?,?,?,?,?)");
        $stmt->bindValue(1, $name);
        $stmt->bindValue(2, $email);
        $stmt->bindValue(3, $thumbnail_url);
        $stmt->bindValue(4, password_hash($password, PASSWORD_DEFAULT));
        $stmt->bindValue(5, date("Y/m/d H:i:s"));

        return ($stmt->execute()) ? $this->getUserByEmail($email) : false;
    }

    public function getUser($id)
    {
        return $this->dbh->query('SELECT * FROM users WHERE id="' . $id . '"')->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserByEmail($email)
    {
        return $this->dbh->query('SELECT * FROM users WHERE email="' . $email . '"')->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserByEmailAndPassword($email, $password)
    {
        $user = $this->getUserByEmail($email);
        return (password_verify($password, $user["password"])) ? $user : false;
    }
}
