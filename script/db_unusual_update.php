<?php
set_time_limit(0); 

require_once("../config/config.php");

$csvFile = __DIR__ ."/../data/cccc.csv";

try{
    $pdo->beginTransaction();

    $update_postal_stmt = $pdo->prepare("UPDATE postal_codes SET postal_code = ? WHERE prefecture = ? AND city = ? AND town = ?");
    $update_address_stmt = $pdo->prepare('UPDATE postal_codes SET prefecture = ? AND city = ? AND town = ? WHERE postal_code = ?' );

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
            //先に住所の更新
            $update_address_stmt->execute([$postal_data[2], $postal_data[6], $postal_data[7], $postal_data[8]]);
            $address_updated = $update_address_stmt->rowCount() > 0;

            //次に郵便番号
            if (!$address_updated) {
                $update_postal_stmt->execute([$postal_data[2], $postal_data[6], $postal_data[7], $postal_data[8]]);
                $postal_updated = $update_postal_stmt->rowCount() > 0;
            } else{
                $postal_updated = false;
            }

            //どっちもできなかったら新規挿入
            if (!$address_updated && !$postal_updated) {
                $insert_stmt->execute([$postal_data[2], $postal_data[6], $postal_data[7], $postal_data[8]]);
            }
            //同じ住所や郵便番号が複数存在することがない前提の処理、アップデート用の資料に郵便番号もしくは住所が同じものが存在している場合を確認する処理が必要
        fclose($handle);
        }
    }else {
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