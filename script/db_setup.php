<?php
set_time_limit(0); 

require_once("../config/config.php");

$csvFile = __DIR__ ."/../data/utf_ken_all.csv";

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
    $handle = fopen($csvFile, "r");

    if ($handle !== FALSE) {
        while (($postal_data = fgetcsv($handle)) !== FALSE) {
            if (count($postal_data) !== 15) {
                $error_list[] = $postal_data;
                continue;
            }
            if ($postal_data[8]=="以下に掲載がない場合") {
                $postal_data[8] = "";
            }
            $stmt->execute([$postal_data[2], $postal_data[6], $postal_data[7], $postal_data[8]]);
        }
        fclose($handle);
    } else {
        throw new Exception("CSVファイルを開くことができませんでした。");
    }

    $pdo->commit();
    echo "データのセットアップが完了しました。" . PHP_EOL;
} catch(Exception $e){
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    // $pdo->rollBack();
    echo "データのセットアップが失敗しました。". $e->getMessage() . PHP_EOL;
}