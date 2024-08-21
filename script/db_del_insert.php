<?php
set_time_limit(0); 

require_once("../config/config.php");

$insert_csvFile = __DIR__ ."/../data/aaaa.csv";
$delete_csvFile = __DIR__ ."/../data/bbbb.csv";

try{
    $pdo->beginTransaction();

    $insert_stmt = $pdo->prepare("INSERT INTO postal_codes (postal_code, prefecture, city, town) VALUES (?, ?, ?, ?) ");
    $delete_stmt = $pdo->prepare("DELETE FROM postal_codes WHERE postal_code = ? AND prefecture = ? AND city = ? AND town = ?");

    $error_list = [];
    $delete_handle = fopen($delete_csvFile,"r");

    if($delete_handle !== false){
        while (($postal_data = fgetcsv($delete_handle)) !== FALSE) {
            if (count($postal_data) !== 15) {
                $error_list[] = $postal_data;
                continue;
            }
            if ($postal_data[8]=="以下に掲載がない場合") {
                $postal_data[8] = "";
            }
            $delete_stmt->execute([$postal_data[2], $postal_data[6], $postal_data[7], $postal_data[8]]);
            $deleted_data = $delete_stmt->rowCount() > 0;
            if (!$deleted_data) {
                $error_list[] = $postal_data;
            }
        }
        if ($error_list) {
            var_dump($error_list);
            $error_list = [];
        }
    }else {
        throw new Exception("デリート用のCSVファイルを開くことができませんでした。");
    }
    
    $insert_handle = fopen($insert_csvFile,"r");
    if ($delete_handle !== False) {
        while (($postal_data = fgetcsv($insert_handle)) !== FALSE){
            if (count($postal_data) !== 15) {
                $error_list[] = $postal_data;
                continue;
            }
            if ($postal_data[8]=="以下に掲載がない場合") {
                $postal_data[8] = "";
            }
            $insert_stmt->execute([$postal_data[2], $postal_data[6], $postal_data[7], $postal_data[8]]);
            $insert_data = $insert_stmt->rowCount() > 0;
            if (!$insert_data) {
                $error_list[] = $postal_data;
            }
        }
        if ($error_list) {
            var_dump($error_list);
        }
    }else {
        throw new Exception("インサート用のCSVファイルを開くことができませんでした。");
    }
    $pdo->commit();
    echo "データのセットアップが完了しました。" . PHP_EOL;
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    // $pdo->rollBack();
    echo "データのセットアップが失敗しました。". $e->getMessage() . PHP_EOL;
}