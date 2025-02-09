<?php

require __DIR__ . '/../../WebProject/vendor/autoload.php';
require '../vendor/autoload.php';

/**
 * 더 최신 버전의 lang 파일에서 삭제된 것은 removed 로 정리되는데, 이것들은 파일에서 삭제한다.
 */
try {
    // 소요 시간 측정하기 위해서 시각 추가
    echo date('Y-m-d H:i:s') . "\n";

    // 새로 추가된 부분 조회
    $file = fopen("../Design/en45pts.lang.removed.csv", 'r');
    if ($file === false) {
        throw new Exception("Unable to open file!");
    }
    $host = 'host.docker.internal';
    $dbname = 'laravel';
    $username = 'root';
    $password = 'korean@local';

    // PDO 객체 생성
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 번역, 변환해서 저장
    $counter = 0;
    while (($line = fgetcsv($file)) !== FALSE) {
        // CSV 파일에서 데이터 조회
        $langId = $line[0];
        $unknown = $line[1];
        $index = $line[2];
        $offset = $line[3];

        // DELETE 쿼리
        $sql = "
            DELETE FROM `lang_id_unknown_index_offsets`
            WHERE `lang_id` = {$langId} 
                AND `unknown` = {$unknown} 
                AND `index` = {$index}
                -- AND `offset` = {$offset} offset 은 removed.csv 파일에서 무의미하게 나옴
            ;";

        // Prepared Statement 사용
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute() === false) {
            throw new Exception("delete fail, $langId, $unknown, $index, $offset");
        }

        // 진행 확인용 문구 출력한다.
        $counter++;
        echo "$counter, $langId, $unknown, $index, $offset done";
        print("\n");
    }

    // 정리
    fclose($file);
    echo date('Y-m-d H:i:s') . "\n";

} catch (Exception $e) {
    echo $e->getCode();
    echo $e->getMessage();
}