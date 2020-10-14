<?php

require __DIR__ . "/../Controller/Auth.php";
require __DIR__ . "/../Model/Thread.php";
require __DIR__ . "/../Model/Comment.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // csrf_tokenエラー
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_POST["csrf_token"]) || $_POST["csrf_token"] !== $_SESSION['csrf_token']) {
        throw new Exception('不正なリクエストです。');
    }
    Auth::logout();

    header("Location: http://" . $_SERVER["HTTP_HOST"] . "/login.php");
}
