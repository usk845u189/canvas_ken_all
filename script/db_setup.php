<?php
require_once("..\config\config.php");

$csvFile = __DIR__ ."\..\data\utf_ken_all_utf8.csv";

try{
    $pdo->beginTransaction();

    $pdo->exec("CREATE TABLE IF NOT EXISTS postal_codes (
        id INT AUTO_INCREMENT PRIMARY KEY, 
        postal_code VARCHAR(7) NOT NULL, 
        prefecture VARCHAR(100) NOT NULL, 
        city VARCHAR(100) NOT NULL, 
        town VARCHAR(100) NOT NULL
    )");

    $stmt = $pdo->prepare("INSERT INTO postal_codes (postal_code, prefecture, city, town) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE prefecture=VALUES(prefecture), city=VALUES(city), town=VALUES(town)");

    $error_list = [];
    if (($handle=fopen($csvFile,"r")) !== FALSE) {
        while (($postal_data = fgetcsv($handle)) !== FALSE) {
            if (count($hanle) !== 14){
                $error_list[] = $postal_data;
                continue;
            }
            $stmt->execute([$postal_data[2], $postal_data[6], $postal_data[7], $postal_data[8]]);
        }
        fclose($handle);
    }

    $pdo->commit();
    echo "データのセットアップが完了しました。" . PHP_EOL;
    var_dump($error_list);
} catch(Exception $e){
    $pdo->rollBack();
    echo "Failed". $e->getMessage() . PHP_EOL;
}