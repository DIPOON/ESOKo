<?php

require __DIR__ . '/../../WebProject/vendor/autoload.php';
require '../vendor/autoload.php';

/**
 * en_text 칼럼을 롤백과 번역할 때 볼 수 있도록 추가하려는데, 기존 데이터에는 해당 값이 없어서
 */
try {
    // 소요 시간 측정하기 위해서 시각 추가
    echo date('Y-m-d H:i:s') . "\n";

    // 새로 추가된 부분 조회
    $file = fopen("../Design/en45.lang.csv", 'r');
    if ($file === false) {
        throw new Exception("Unable to open file!");
    }
    $host = 'host.docker.internal';
    $dbname = 'laravel';
    $username = 'root';
    $password = 'korean@local';

    $cho = "\\";

    // PDO 객체 생성
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password); //
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 번역, 변환해서 저장
    $counter = 0;
    while (($line = fgetcsv($file)) !== FALSE) {
        // CSV 파일에서 데이터 조회
        $langId = $line[0];
        $unknown = $line[1];
        $index = $line[2];
//        $offset = $line[3]; 안씀
        $enText = $line[4];

        // UPDATE 쿼리
        $sql = "
            UPDATE `laravel`.`lang_id_unknown_index_offsets` 
            SET `en_text` = :en_text
            WHERE `lang_id` = {$langId} 
                AND `unknown` = {$unknown} 
                AND `index` = {$index}
            ;";

        // Prepared Statement 사용
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':en_text', $enText);
        if ($stmt->execute() === false) {
            throw new Exception("update en text fail, $langId, $unknown, $index");
        }

        // 진행 확인용 문구 출력한다.
        $counter++;
        echo "$counter, $langId, $unknown, $index done";
        print("\n");
    }

    // 정리
    fclose($file);
    echo date('Y-m-d H:i:s') . "\n";

} catch (Exception $e) {
    echo $e->getCode();
    echo $e->getMessage();
}