<?php
ini_set('display_errors', "On");
require_once __DIR__ . "/process/Controller/Auth.php";
require_once __DIR__ . "/process/Model/Thread.php";
require __DIR__ . "/process/Controller/FileController.php";

// ログインしているかどうか確認
if (!Auth::getLoginUser()) {
    header('Location: http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . './login.php');
    exit();
}

$threadValue = null;

// GETリクエスト
$type = isset($_GET['type']) ? htmlentities($_GET['type'], ENT_QUOTES, 'UTF-8') : null;
$thread_id = isset($_GET['thread_id']) ? htmlentities($_GET['thread_id'], ENT_QUOTES, 'UTF-8') : null;

if ($type === "edit" && $thread_id) {
    $threadModel = new Thread();
    $user_id = Auth::getLoginUser()['id'];
    $thread = $threadModel->getThreadByIdAndUser($thread_id, $user_id);
    $threadValue = $thread['content'];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // csrf_tokenエラー
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_POST["csrf_token"]) || $_POST["csrf_token"] !== $_SESSION['csrf_token']) {
        throw new Exception('不正なリクエストです。');
    }

    $threadExists = isset($_POST['thread_content']);
    $threadValue = isset($_POST['thread_content']) ? htmlentities($_POST['thread_content'], ENT_QUOTES, 'UTF-8') : null;
    $image = isset($_FILES['thread_file']['name']) ? $_FILES['thread_file']['name'] : null;


    $errors = [];

    if ($threadExists && !$threadValue) {
        $errors[] = 'スレッド内容が入力されていません。';
    }
    if ($threadExists && mb_strlen($threadValue) > 140) {
        $errors[] = 'スレッド内容は140文字以下で入力してください。';
    }

    // 画像のアップロード処理
    if ($image) {
        // プロフィール写真保管用のディレクトリを作成
        $path = dirname(__FILE__) . "/images/thread/";
        if (!file_exists($path)) {
            mkdir($path, 0777);
        }
        $savedPath ="/images/thread/";
        $imagePath = FileController::fileUpload($_FILES['thread_file'], $path, $savedPath); // ファイルアップロード
    }

    // 登録完了したらindex.phpへリダイレクト
    if ($threadExists && count($errors) === 0) {
        $imageUrl = isset($imagePath) ? $imagePath : null;
        $threadModel = new Thread();

        if ($type === 'edit' && $thread_id) {
            if (!$threadModel->update($thread['thread.id'], $thread['user_id'], $threadValue, $imageUrl)) {
                $errors[] = "スレッド編集に失敗しました。";
            } else {
                header('Location: http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . './index.php');
                exit();
            }
        } else {
            if (!$threadModel->create($threadValue, $imageUrl)) {
                $errors[] = "スレッド投稿に失敗しました。";
            } else {
                header('Location: http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . './index.php');
                exit();
            }
        }
    }
}

// csrf_token
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$csrf_token = substr(str_shuffle('1234567890abcdefghijklmnopqrstuvwxyz'), 0, 32);
$_SESSION['csrf_token'] = $csrf_token;

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/style.css">
    <title>掲示板 | スレッド作成</title>
</head>
<body>
    <div id="header">
        <?php if ($type === "edit") :?>
            <div class="nav-back"><a href="<?php echo '/threadDetail.php?thread=' . $thread_id ; ?>" class="">スレッドに戻る</a></div>
        <?php else : ?>
            <div class="nav-back"><a href="/" class="">スレッド一覧に戻る</a></div>
        <?php endif; ?>
        <?php if (Auth::getLoginUser()) : ?>
        <form action="/process/process/logout.php" method="post" name="logout_form">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <div class="navbar-item"><a href="javascript:logout_form.submit()">ログアウト</a></div>
        </form>
        <?php endif; ?>
    </div>
    <div class="page-main">
        <h1 class="page-title">スレッドをたてる</h1>
        <?php if (isset($errors) && count($errors) > 0) : ?>
        <div class="validation-errors">
            <?php foreach ($errors as $error) : ?>
            <div class="validation-error">・<?php echo $error; ?></div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php $query = isset($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '' ?>
        <div>
            <!-- Todo: csrf対策 -->

            <textarea name="thread_content" id="thread-post" rows="15" placeholder="話題にしたいことを書いてください" form="thread-post-form"><?php echo $threadValue; ?></textarea>
            <?php if ($type !== "edit" || (isset($thread) && !$thread['image_url'])) : ?>
                <input type="file" name="thread_file" accept="image/*" form="thread-post-form">
            <?php else : ?>
                <form action="/process/process/thread/removeImg.php" method="post" name="img_delete_form">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" name="thread_id" value="<?php echo $thread['thread.id'];?> ">
                    <input type="hidden" name="redirect_url" value="<?php echo '/post.php' . $query ?>">
                    <a class="delete-post-button" href="javascript:img_delete_form.submit()">画像を削除する</a>
                </form>
                <div class="edit-post-image"><img src="<?php echo $thread['image_url'] ?>" alt=""></div>
            <?php endif; ?>
        </div>
        <form action="<?php echo 'post.php' . $query ?>" method="post" id="thread-post-form" enctype="multipart/form-data">
            <div id="submit-button-wrapper">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <button type="submit" id="thread-post-submit">投稿</button>
            </div>
        </form>

    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
</body>
</html>