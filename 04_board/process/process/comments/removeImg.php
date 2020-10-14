<?php

require __DIR__ . "/../../Controller/Auth.php";
require __DIR__ . "/../../Model/Thread.php";
require __DIR__ . "/../../Model/Comment.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // csrf_tokenエラー
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_POST["csrf_token"]) || $_POST["csrf_token"] !== $_SESSION['csrf_token']) {
        throw new Exception('不正なリクエストです。');
    }

    $comment_id = isset($_POST['comment_id']) ? $_POST['comment_id'] : null;
    $redirect_url = isset($_POST['redirect_url']) ? $_POST['redirect_url'] : null;

    $comment = new Comment;
    $comment->removeImg($comment_id);

    return header("Location: http://" . $_SERVER["HTTP_HOST"] . $redirect_url);
}
