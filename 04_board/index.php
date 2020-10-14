<?php
ini_set('display_errors', "On");
require __DIR__ . "/process/Controller/Auth.php";
require __DIR__ . "/process/Model/Thread.php";

// スレッド一覧を取得
$threadModel = new Thread();
$threads = $threadModel->getThreads();

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
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">
    <title>掲示板 | TOP</title>
</head>
<body>
    <div id="header">
    <div class="nav-back"><a href="/" class=""></a></div>
        <?php if (Auth::getLoginUser()) : ?>
        <form action="/process/process/logout.php" method="post" name="logout_form">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <div class="navbar-item"><a href="javascript:logout_form.submit()">ログアウト</a></div>
        </form>
        <?php else : ?>
            <div class="navbar-item"><a href="/login.php">ログイン</a></div>
        <?php endif; ?>
    </div>
    <div class="page-main">
        <h1 class="page-title">みんなの掲示板</h1>
        <div id="page-index-subtitle">
            <h2>スレッド一覧</h2>
            <?php if (Auth::getLoginUser()) : ?>
                <a href="/post.php" id="thread-post-nav"><i class="fas fa-pen-alt"></i></a>
            <?php endif; ?>
        </div>

        <?php if (count($threads) === 0) : ?>
            <div>スレッドはありません-----------。</div>
        <?php endif; ?>
        <?php foreach ($threads as $thread) : ?>
        <div class="thread-list">
            <div class="thread-content"><a href="/threadDetail.php?thread=<?php echo htmlentities($thread['thread.id'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlentities($thread['content'], ENT_QUOTES, 'UTF-8'); ?></a></div>
            <?php if($thread['image_url']) : ?>
                <div class="index-thread-image"><img src="<?php echo htmlentities($thread['image_url'], ENT_QUOTES, 'UTF-8'); ?>" alt=""></div>
            <?php endif; ?>
            <div class="thread-footer">
                <div class="user-image" id="thread-user-image" style='background: url("<?php echo htmlentities($thread['thumbnail_url'], ENT_QUOTES, 'UTF-8'); ?>")'></div>
                <div class="thread-user-name"><?php echo htmlentities($thread['name'], ENT_QUOTES, 'UTF-8'); ?></div>
                <div class="thread-posted-at"><?php echo htmlentities($thread['thread.created_at'], ENT_QUOTES, 'UTF-8'); ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
</body>
</html>