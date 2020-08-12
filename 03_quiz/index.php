<?php
session_start();
require('./const.php');

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
        <form action="./answer.php" method="POST" id="quiz-form" class="mt-4">
            <div class="form-group">
                <label for="answer1"><?php echo(QUIZ['quiz1']['quiz']); ?></label>
                <input type="text" class="form-control" id="answer1" value="<?php isset($_SESSION['answer1']) ? htmlentities($_SESSION['answer1']) : '' ?>" name="answer1">
            </div>

            <div class="form-group">
                <label><?php echo(QUIZ['quiz2']['quiz']); ?></label>
                <?php foreach (QUIZ['quiz2']['check-content'] as $quiz2) : ?>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="<?php echo($quiz2['id']); ?>" name="answer2[]" value="<?php echo($quiz2['choice']); ?>">
                    <label class="form-check-label" for="<?php echo($quiz2['id']); ?>"><?php echo($quiz2['choice']); ?></label>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="form-group">
                <label><?php echo(QUIZ['quiz3']['quiz']); ?></label>
                <?php foreach (QUIZ['quiz3']['check-content'] as $key => $quiz3) : ?>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="answer3" id="<?php echo($quiz3['id']); ?>" value="<?php echo($quiz3['choice']); ?>">
                    <label class="form-check-label" for="<?php echo($quiz3['id']); ?>">
                        <?php echo($quiz3['choice']); ?>
                    </label>
                </div>
                <?php endforeach; ?>
            </div>
            <button class="btn btn-primary" type="submit">送信</button>
        </form>
    </div>
</body>
</html>