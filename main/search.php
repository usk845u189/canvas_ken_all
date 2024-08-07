<?php
$x = $_POST["x"];
$y = $_POST["y"];

$post_address = $x  . $y;

$address_list = [];

$fp = fopen("utf_ken_all.csv","r");
$line = fgetcsv($fp);
while ($line != false) {
    if ($line[2] === $post_address) {
        $address_list[] = $line;
    }
    $line = fgetcsv($fp);
}

fclose($fp);

if (empty($address_list)) {
    header("Location: error.html");
    exit();
}
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
        <?php for ($i=0; $i < count($address_list); $i++) { ?>
            <tr>
                <td><?php echo $address_list[$i][2] ?></td>
                <td><?php echo $address_list[$i][6] . $address_list[$i][7] .$address_list[$i][8] ?></td>
            </tr>
        <?php } ?>
    </table>
    <a href="top.html">戻る</a>
</body>
</html>