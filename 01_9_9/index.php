<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>9x9?</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <table border="1">
        <tr>
            <th></th>
            <th>1</th>
            <th>2</th>
            <th>3</th>
            <th>4</th>
            <th>5</th>
            <th>6</th>
            <th>7</th>
            <th>8</th>
            <th>9</th>
        </tr>
        <?php for ($i=1; $i<=9; $i++) : ?>
        <tr>
            <th><?php echo $i; ?></th>
            <?php for ($j=1; $j<=9; $j++) : ?>
                <td class="<?php echo (($i*$j)%2 == 0) ? 'bg-aquamarine' : '' ?>"><?php echo ($i * $j) ?></td>
            <?php endfor; ?>
        </tr>
        <?php endfor; ?>
    </table>
</body>
</html>