<?php
require_once("../config/config.php");

$x = filter_input(INPUT_POST,"x", FILTER_SANITIZE_STRING);
$y = filter_input(INPUT_POST,"y", FILTER_SANITIZE_STRING);

$postal_code = htmlspecialchars($x)  . htmlspecialchars($y);

if (strlen($postal_code) != 7) {
    header("Location: error.html");
    exit();
}

$stmt = $pdo->prepare("SELECT postal_code, prefecture, city, town FROM postal_codes WHERE postal_code = :postal_code");
$stmt->execute(['postal_code'=>$postal_code]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADDRESS SEARCH</title>
</head>
<body>
    <h3>RESULT</h3>
    <hr>
    <table border="1">
        <tr>
            <th>郵便番号</th>
            <th>住所</th>
        </tr>
        <?php foreach ($results as $row) { ?>
            <tr>
                <td><?php echo $row['postal_code'] ?></td>
                <td><?php echo $row['prefecture'] . $row['city'] . $row['town'] ?></td>
            </tr>
        <?php } ?>
    </table>
    <a href="index.html">戻る</a>
</body>
</html>