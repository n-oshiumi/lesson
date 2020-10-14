<?php
ini_set('display_errors', "On");

require __DIR__ . "/process/Model/User.php";
require __DIR__ . "/process/Controller/Auth.php";

// ログインしているかどうか確認
if (Auth::getLoginUser()) {
    header('Location: http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . './index.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $emailExists = isset($_POST['email']);
    $emailValue = isset($_POST['email']) ? htmlentities($_POST['email'], ENT_QUOTES, 'UTF-8') : null;

    $passwordExists = isset($_POST['password']);
    $passwordValue = isset($_POST['password']) ? htmlentities($_POST['password'], ENT_QUOTES, 'UTF-8') : null;

    $errors = []; // エラー初期化

    // バリデーション
    if ($emailExists && !$emailValue) {
        $errors[] = 'メールアドレスを入力してください。';
    }

    if ($emailValue && !preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $emailValue)) {
        $errors[] = 'メールアドレスの形式が正しくありません。';
    }

    if ($passwordExists && !$passwordValue) {
        $errors[] = 'パスワードを入力してください。';
    }

    $user = new User();
    // csrf_tokenエラー
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_POST["csrf_token"]) || $_POST["csrf_token"] !== $_SESSION['csrf_token']) {
        throw new Exception('不正なリクエストです。');
    }

    if ($emailExists && $passwordExists && count($errors) === 0) {
        $logginUser = $user->getUserByEmailAndPassword($emailValue, $passwordValue);
        if (!$user->getUserByEmailAndPassword($emailValue, $passwordValue)) {
            $errors[] = 'ユーザーが見つかりません。';
        } else {
            Auth::login($logginUser["id"]);
            header('Location: http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . './index.php');
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
    <link rel="stylesheet" href="/css/style.css">
    <title>掲示板 | ログイン</title>
</head>
<body>
    <div class="page-main">
        <h1 class="page-title">ログイン</h1>
        <?php if (isset($errors) && count($errors) > 0) : ?>
        <div class="validation-errors">
            <?php foreach ($errors as $error) : ?>
            <div class="validation-error">・<?php echo $error; ?></div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <form action="login.php" method="post" id="user-register-form">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

            <div class="user-register-block">
                <label for="user-email" class="register-label">メールアドレス</label>
                <input type="text" name="email" id="user-email" class="register-input">
            </div>

            <div class="user-register-block">
                <label for="user-password" class="register-label">パスワード</label>
                <input type="password" name="password" id="user-password" class="register-input">
            </div>

            <div id="register-button-wrapper">
                <button type="submit" id="register-submit">ログイン</button>
                <a href="/register.php" class="register-login-link">会員登録はこちら</a>
            </div>
        </form>

    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
</body>
</html>