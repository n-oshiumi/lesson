<?php
session_start();
require('./const.php');

// 問題1をセッションに格納
if (isset($_POST['answer1'])) {
    $_SESSION['answer1'] = $_POST['answer1'];
}

// 問題2をセッションに格納
if (isset($_POST['answer2']) && is_array($_POST['answer2'])) {
    $_SESSION['answer2'] = $_POST['answer2'];
}

// 問題3をセッションに格納
if (isset($_POST['answer3'])) {
    $_SESSION['answer3'] = $_POST['answer3'];
}


function checkTextSame($text1, $text2) {
    return strtolower(trim($text1)) === strtolower(trim($text2));
}

function checkArraySame($array1, $array2) {
    return empty(array_diff($array1, $array2));
}

$correct_text = '正解です！';
$incorrect_text = '不正解です！'

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Quiz!</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <div id="quiz-wrapper" class="mx-auto mt-5">
        <h1 class="text-center">クイズ！</h1>
        <form action="./answer.php" id="quiz-form" class="mt-4">
            <div class="form-group">
                <label><?php echo(QUIZ['quiz1']['quiz'])?></label>
                <div>あなたの回答：<?php echo(htmlentities($_SESSION['answer1'])); ?></div>
                <div>正解：<?php echo(QUIZ['quiz1']['correct-answer']); ?></div>
                <div>結果：<?php echo(checkTextSame($_SESSION['answer1'], QUIZ['quiz1']['correct-answer']) ? $correct_text : $incorrect_text) ?></div>
            </div>

            <div class="form-group">
                <label><?php echo(QUIZ['quiz2']['quiz'])?></label>
                <div>あなたの回答：<?php echo (htmlentities(implode('、', $_SESSION['answer2']))); ?></div>
                <div>正解：<?php echo(implode('、', QUIZ['quiz2']['correct-answer'])); ?></div>
                <div>結果：<?php echo((checkArraySame($_SESSION['answer2'], QUIZ['quiz2']['correct-answer'])) ? $correct_text : $incorrect_text); ?></div>
                <div>解説：プログラミング言語のPHPの正式名称は「PHP: Hypertext Preprocessor」らしい。出版社のPHP研究所のPHPは「Peace and Happiness through Prosperity」らしい。</div>
            </div>

            <div class="form-group">
                <label><?php echo(QUIZ['quiz3']['quiz'])?></label>
                <div>あなたの回答：<?php echo(htmlentities($_SESSION['answer3'])); ?></div>
                <div>正解：<?php echo(QUIZ['quiz3']['correct-answer']); ?></div>
                <div>結果：<?php echo(($_SESSION['answer3'] === QUIZ['quiz3']['correct-answer']) ? $correct_text : $incorrect_text); ?></div>
                <div>「ブレンダン・アイク氏」はJavaScriptの生みの親らしい。「ラスマス・ラードフ氏」はPHPの生みの親らしい。「まつもと ゆきひろ氏」はRubyの生みの親。</div>
            </div>
            <a href="./index.php" class="btn btn-primary text-white">戻る</a>
        </form>
    </div>
</body>
</html>