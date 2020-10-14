<?php
ini_set('display_errors', "On");
require __DIR__ . "/process/Controller/Auth.php";
require __DIR__ . "/process/Model/Thread.php";
require __DIR__ . "/process/Model/Comment.php";
require __DIR__ . "/process/Controller/FileController.php";


// GETリクエスト
$threadExists = isset($_GET['thread']);
$threadValue = isset($_GET['thread']) ? htmlentities($_GET['thread'], ENT_QUOTES, 'UTF-8') : null;

// スレッド取得
$threadModel = new Thread();
$thread = $threadModel->getThread($threadValue);

if (!$threadExists || !$thread) {
    header('Location: http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . './index.php');
    exit();
}

// コメント取得
$commentModel = new Comment();
$comments = $commentModel->getComments($thread['thread.id']);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // csrf_tokenエラー
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_POST["csrf_token"]) || $_POST["csrf_token"] !== $_SESSION['csrf_token']) {
        throw new Exception('不正なリクエストです。');
    }

    // POSTリクエスト
    $thread_id = isset($_POST['thread_id']) ? htmlentities($_POST['thread_id'], ENT_QUOTES, 'UTF-8') : null;
    $comment = isset($_POST['thread_comment']) ? htmlentities($_POST['thread_comment'], ENT_QUOTES, 'UTF-8') : null;
    $image = isset($_FILES['comment_image']['name']) ? $_FILES['comment_image']['name'] : null;

    $info = [];
    $errors = [];

    if (!$thread_id) {
        $errors[] = 'リクエストが無効です';
    }

    if (!$comment) {
        $errors[] = 'コメントを入力してください';
    }

    if ($comment && mb_strlen($comment) > 140) {
        $errors[] = 'コメントは140文字以下で入力してください。';
    }


    // 登録完了したらindex.phpへリダイレクト
    if ($thread_id && $comment && count($errors) === 0) {
        // 画像のアップロード処理
        $imagePath = null;
        if ($image) {
            // プロフィール写真保管用のディレクトリを作成
            $path = dirname(__FILE__) . "/images/comment/";
            if (!file_exists($path)) {
                mkdir($path, 0777);
            }
            $savedPath ="/images/comment/";
            $imagePath = FileController::fileUpload($_FILES['comment_image'], $path, $savedPath); // ファイルアップロード
        }

        $commentModel = new Comment();
        if (!$commentModel->create($thread_id, $comment, $imagePath)) {
            $errors[] = "コメント投稿に失敗しました。";
        } else {
            $info[] = "コメントを投稿しました！";
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
    <title>掲示板 | スレッド内容</title>
</head>
<body>
    <div id="header">
        <div class="nav-back"><a href="/" class="">スレッド一覧に戻る</a></div>
        <?php if (Auth::getLoginUser()) : ?>
        <form action="/process/process/logout.php" method="post" name="logout_form">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <div class="navbar-item"><a href="javascript:logout_form.submit()">ログアウト</a></div>
        </form>
        <?php else: ?>
            <div class="navbar-item"><a href="/login.php">ログイン</a></div>
        <?php endif; ?>
    </div>
    <div class="page-main">
        <h1 class="page-title">スレッド！</h1>

        <div id="page-post-title">スレッド内容</div>
        <div class="board-list" id="user-post-content">
            <div class="board-header">
                <div class="board-user-image user-image" style='background: url("<?php echo $thread['thumbnail_url']; ?>")'></div>
                <div class="board-right-user-info">
                    <div class="board-user-name"><?php echo $thread['name']; ?></div>
                    <div class="board-posted-at">投稿：<?php echo $thread['thread.created_at']; ?></div>
                </div>
            </div>
            <div class="board-content"><?php echo $thread['content']; ?></div>
            <?php if ($thread['image_url']) : ?>
                <div class="board-content-image"><img src="<?php echo $thread['image_url']; ?> " alt=""></div>
            <?php endif; ?>
            <?php if (Auth::getLoginUser() && Auth::getLoginUser()['id'] === $thread['user_id']) : ?>
            <div class="board-auth-user-content">
                <div class="board-edit"><a href="<?php echo '/post.php?type=edit&thread_id=' . $thread['thread.id']; ?>">編集する</a></div>
                <form action="/process/process/thread/delete.php" name="thread_delete" method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" name="thread_id" value="<?php echo $thread['thread.id']; ?>">
                    <div class="board-delete"><a href="javascript:thread_delete.submit()">削除する</a></div>
                </form>
            </div>
            <?php endif; ?>
        </div>

        <!-- エラー内容 -->
        <?php if (isset($errors) && count($errors) > 0) : ?>
        <div class="validation-errors">
            <?php foreach ($errors as $error) : ?>
            <div class="validation-error">・<?php echo $error; ?></div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- info内容 -->
        <?php if (isset($info) && count($info) > 0) : ?>
        <div class="info-messages">
            <?php foreach ($info as $info) : ?>
            <div class="info-message">・<?php echo $info; ?></div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>


        <?php if (Auth::getLoginUser()) : ?>
        <div id="page-comment-create">
            <form action="<?php echo 'threadDetail.php?thread=' . $thread['thread.id'] ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                <textarea name="thread_comment" id="thread-comment-create" rows="3" placeholder="コメントを記入"></textarea>
                <input type="hidden" name="thread_id" value="<?php echo $thread['thread.id']; ?>">
                <div id="thread-comment-post-button-wrapper">
                    <input type="file" name="comment_image" accept="image/*">
                    <button type="submit">コメントする</button>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <div id="page-board-title">コメント</div>
        <div id="board-list-wrapper">
            <?php if (count($comments) === 0) : ?>
                <div style="margin-top: 16px; color: orange;">コメントはまだありませんよー。</div>
            <?php endif; ?>
            <?php foreach ($comments as $comment) : ?>
            <div class="board-list">
                <div class="board-header">
                    <div class="user-image" style='background: url("<?php echo $comment['thumbnail_url']; ?>")'></div>
                    <div class="board-right-user-info">
                        <div class="board-user-name"><?php echo $comment['name']; ?></div>
                        <div class="board-posted-at">投稿：<?php echo $comment['comments.created_at']; ?></div>
                    </div>
                </div>
                <form action="/process/process/comments/update.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" name="comment_id" value="<?php echo $comment['comment.id']; ?>">
                    <input type="hidden" name="thread_id", value="<?php echo $thread['thread.id']; ?>">
                    <textarea name="comment-content" class="comment-content" id="" readonly><?php echo $comment['content']; ?></textarea>
                    <?php if ($comment['image_url']) : ?>
                        <div class="comment-image"><img src="<?php echo $comment['image_url']; ?>" alt=""></div>
                    <?php else : ?>
                        <input type="file" name="comment_upload_file" accept="image/*" class="comment-update mg-8">
                    <?php endif; ?>
                    <button type="submit" class="comment-update">更新</button>
                </form>
                <?php if (Auth::getLoginUser() && $comment['user_id'] === Auth::getLoginUser()['id']) : ?>
                <div class="board-auth-user-content">
                    <?php if ($comment['image_url']) : ?>
                    <form action="/process/process/comments/removeImg.php" method="post" name="remove_comment_image_form">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <input type="hidden" name="comment_id" value="<?php echo $comment['comment.id'] ?>">
                        <input type="hidden" name="redirect_url" value="<?php echo "/threadDetail.php?thread=" . $thread['thread.id']; ?>">
                        <a href="javascript:remove_comment_image_form.submit()" class="delete-comment-image">画像を削除する</a>
                    </form>
                    <?php endif; ?>
                    <div class="comment-edit"><a >編集する</a></div>
                    <form action="/process/process/comments/delete.php" name="comment_delete" method="post">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <input type="hidden" name="thread_id", value="<?php echo $thread['thread.id']; ?>">
                        <input type="hidden" name="comment_id", value="<?php echo $comment['comment.id']; ?>">
                        <div class="board-delete"><a href="javascript:comment_delete.submit()">削除する</a></div>
                    </form>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="/js/comment_edit.js"></script>
</body>
</html>