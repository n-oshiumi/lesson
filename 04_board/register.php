<?php
ini_set('display_errors', "On");

require __DIR__ . "/process/Model/User.php";
require __DIR__ . "/process/Controller/Auth.php";
require __DIR__ . "/process/Controller/FileController.php";

// ログインしているかどうか確認
if (Auth::getLoginUser()) {
    header('Location: /index.php');
    exit();
}

$nameExists = isset($_POST['name']);
$nameValue = isset($_POST['name']) ? htmlentities($_POST['name'], ENT_QUOTES, 'UTF-8') : null;

$emailExists = isset($_POST['email']);
$emailValue = isset($_POST['email']) ? htmlentities($_POST['email'], ENT_QUOTES, 'UTF-8') : null;

$passwordExists = isset($_POST['password']);
$passwordValue = isset($_POST['password']) ? htmlentities($_POST['password'], ENT_QUOTES, 'UTF-8') : null;

$passwordConfirmExists = isset($_POST['password_confirm']);
$passwordConfirmValue = isset($_POST['password_confirm']) ? htmlentities($_POST['password_confirm'], ENT_QUOTES, 'UTF-8') : null;

$image = isset($_FILES['user_image']['name']) ? $_FILES['user_image']['name'] : null;



if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // csrf_tokenエラー
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_POST["csrf_token"]) || $_POST["csrf_token"] !== $_SESSION['csrf_token']) {
        throw new Exception('不正なリクエストです。');
    }



    $errors = []; // エラー初期化


    // バリデーション
    if ($nameExists && !$nameValue) {
        $errors[] = 'アカウント名を入力してください。';
    }

    if ($emailExists && !$emailValue) {
        $errors[] = 'メールアドレスを入力してください。';
    }

    if ($emailValue && !preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $emailValue)) {
        $errors[] = 'メールアドレスの形式が正しくありません。';
    }

    if ($passwordExists && !$passwordValue) {
        $errors[] = 'パスワードを入力してください。';
    }

    if ($passwordValue !== $passwordConfirmValue) {
        $errors[] = 'パスワードとパスワード（確認用）が一致しません。';
    }

    $user = new User();
    if ($user->getUserByEmail($emailValue)) {
        $errors[] = '入力したメールアドレスは既に使用されています。';
    }

    // 画像のアップロード処理
    if ($image) {
        // プロフィール写真保管用のディレクトリを作成
        $path = dirname(__FILE__) . "/images/profile/";
        if (!file_exists($path)) {
            mkdir($path, 0777);
        }
        $savedPath ="/images/profile/";
        $imagePath = FileController::fileUpload($_FILES['user_image'], $path, $savedPath); // ファイルアップロード
    }


    // 登録完了したらindex.phpへリダイレクト
    if ($emailExists && $passwordExists && $passwordConfirmExists && count($errors) === 0) {
        $userImagePath = isset($imagePath) ? $imagePath : "/images/sample/test_image.jpg";
        $createdUser = $user->create($nameValue, $emailValue, $passwordValue, $userImagePath);

        if (!$createdUser) {
            $errors[] = "会員登録に失敗しました。";
        } else {
            Auth::login($createdUser["id"]);
            header('Location: http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . './index.php');
            exit();
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
    <title>掲示板 | 会員登録</title>
</head>
<body>
    <div class="page-main">
        <h1 class="page-title">会員登録</h1>
        <?php if (isset($errors) && count($errors) > 0) : ?>
        <div class="validation-errors">
            <?php foreach ($errors as $error) : ?>
            <div class="validation-error">・<?php echo $error; ?></div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <form action="register.php" method="post" id="user-register-form" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

            <div class="user-register-block">
                <label for="user-name" class="register-label">アカウント名</label>
                <input type="text" name="name" id="user-name" class="register-input" value="<?php echo $nameValue; ?>">
            </div>

            <div class="user-register-block">
                <label for="user-email" class="register-label">メールアドレス</label>
                <input type="text" name="email" id="user-email" class="register-input" value="<?php echo $emailValue; ?>">
            </div>

            <div class="user-register-block">
                <label for="user-password" class="register-label">パスワード</label>
                <input type="password" name="password" id="user-password" class="register-input" value="<?php echo $passwordValue; ?>">
            </div>
            <div class="user-register-block">
                <label for="user-password-confirm" class="register-label">パスワード（再確認）</label>
                <input type="password" name="password_confirm" id="user-password-confirm" class="register-input" value="<?php echo $passwordConfirmValue; ?>">
            </div>

            <div class="user-register-block">
                <label for="user-password-confirm" class="register-label">ユーザー画像</label>
                <input type="file" name="user_image" id="user-image" class="register-input" accept="image/*">
            </div>

            <div id="register-button-wrapper">
                <button type="submit" id="register-submit">会員登録</button>
                <a href="/login.php" class="register-login-link">ログインはこちら</a>
            </div>
        </form>

    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
</body>
</html>