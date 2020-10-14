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

    $thread_id = isset($_POST['thread_id']) ? $_POST['thread_id'] : null;
    $comment_id = isset($_POST['comment_id']) ? $_POST['comment_id'] : null;
    $user_id = Auth::getLoginUser()['id'];

    //　コメント削除
    $comment = new Comment;
    $comment->deleteComment($comment_id, $user_id);

    return header("Location: http://" . $_SERVER["HTTP_HOST"] . "/threadDetail.php?thread=" . $thread_id);
}
