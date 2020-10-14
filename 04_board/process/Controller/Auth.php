<?php
require_once __DIR__ . "/../Model/User.php";

class Auth {

    public static function login($id)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        session_regenerate_id(true);

        $userModel = new User();
        $user = $userModel->getUser($id);
        if (!$user) return false;

        $_SESSION['login_id'] = $id;
        return true;
    }

    public static function getLoginUser()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $userId = isset($_SESSION['login_id']) ? $_SESSION['login_id'] : null;
        if(!$userId) return false;

        $userModel = new User();
        $user = $userModel->getUser($userId);

        return ($user) ? $user : false;
    }

    public static function logout()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['login_id'] = null;
        return true;
    }
}