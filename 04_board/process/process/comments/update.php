<?php

require __DIR__ . "/../../Controller/Auth.php";
require __DIR__ . "/../../Model/Thread.php";
require __DIR__ . "/../../Model/Comment.php";
require __DIR__ . "/../../Controller/FileController.php";

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
    $content = isset($_POST['comment-content']) ? $_POST['comment-content'] : null;
    $image = isset($_FILES['comment_upload_file']) ? $_FILES['comment_upload_file'] : null;


    // 画像のアップロード処理
    $imagePath = null;
    if ($image) {
        // プロフィール写真保管用のディレクトリを作成
        $path = dirname(__FILE__) . "/../../../images/comment/";
        if (!file_exists($path)) {
            mkdir($path, 0777);
        }
        $savedPath ="/images/comment/";
        $imagePath = FileController::fileUpload($_FILES['comment_upload_file'], $path, $savedPath); // ファイルアップロード
    }


    $user_id = Auth::getLoginUser()['id'];

    //　コメント更新
    $comment = new Comment;
    $comment->updateComment($comment_id, $user_id, $content, $imagePath);

    return header("Location: http://" . $_SERVER["HTTP_HOST"] . "/threadDetail.php?thread=" . $thread_id);
}
