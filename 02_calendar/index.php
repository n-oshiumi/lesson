<?php
date_default_timezone_set('Asia/Tokyo');

// ベースのパスを取得する
$baseUrl = $_SERVER['SCRIPT_NAME'];

$now = new DateTime();

// 指定月をセット
$selectedYear = (int)$_GET["year"] ?? $now->format("Y");
$selectedMonth = (int)$_GET["month"] ?? $now->format("m");


// 月の初日をセット
$firstDate = new DateTimeImmutable($selectedYear . "-" . $selectedMonth. "-1");

// 前月・次月を取得する
$previousMonth = $firstDate->modify("-1 months");
$nextMonth = $firstDate->modify("+1 months");
$previousYear = $firstDate->modify("-1 years");
$nextYear= $firstDate->modify("+1 years");



// 曜日の配列を作成
$day_of_week = ["日", "月", "火", "水", "木", "金", "土"];

$w = (int) $firstDate->format('w'); // 最初の曜日を取得
$number_of_days = $firstDate->format("t"); // その月の日数を取得

// 月の日付の配列を作成する
$days_of_month = [];
for ($i=0; $i<$w; $i++) {
    array_push($days_of_month, null);
}
for ($j=1; $j<=$number_of_days; $j++) {
    array_push($days_of_month, $j);
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title>カレンダー</title>
</head>
<body>
    <div id="main">
        <div class="nav-year">
            <div class="nav-year-back"><a href="<?php echo htmlentities($baseUrl . "?year=" . $previousYear->format('Y') . "&month=" . $previousYear->format('m'));  ?>">＜</a></div>
            <h1 id="date-title"><?php echo htmlentities($firstDate->format('Y') . "年" . $firstDate->format('m') . "月"); ?></h1>
            <div class="nav-year-next"><a href="<?php echo htmlentities($baseUrl . "?year=" . $nextYear->format('Y') . "&month=" . $nextYear->format('m'));  ?>">＞</a></div>
        </div>
        <div class="nav-month">
            <div class="nav-month-back"><a href="<?php echo htmlentities($baseUrl . "?year=" . $previousMonth->format('Y') . "&month=" . $previousMonth->format('m'));  ?>">前月</a></div>
            <div class="nav-month-next"><a href="<?php echo htmlentities($baseUrl . "?year=" . $nextMonth->format('Y') . "&month=" . $nextMonth->format('m'));  ?>">次月</a></div>
        </div>
        <table border="1">
            <tr>
                <?php foreach ($day_of_week as $day) : ?>
                <th><?php echo $day; ?></th>
                <?php endforeach; ?>
            </tr>

            <?php foreach (array_chunk($days_of_month, 7) as $days) : ?>
                <tr>
                    <?php foreach (array_pad($days, 7, null) as $day) : ?>
                    <td><?php echo $day; ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>