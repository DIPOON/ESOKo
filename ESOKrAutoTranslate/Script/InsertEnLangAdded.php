<?php

require __DIR__ . '/../../WebProject/vendor/autoload.php';
require '../vendor/autoload.php';

use App\Enum\EnumState;
use App\Enum\EnumUser;

/**
 * 새로운 en.lang 의 added 를 lang_id_unknown_index_offsets 테이블에 넣는다.
 */
try {
    // 소요 시간 측정하기 위해서 시각 추가
    echo date('Y-m-d H:i:s') . "\n";

    // 새로 추가된 부분 조회
    $file = fopen("../Design/en45pts.lang.added.csv", 'r');
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

    // 추가
    $counter = 0;
    while (($line = fgetcsv($file)) !== FALSE) {
        // 카운터는 일단 올리고 고민한다.
        $counter++;

        // CSV 파일에서 데이터 조회
        $langId = $line[0];
        $unknown = $line[1];
        $index = $line[2];
        $offset = $line[3];
        $text = $line[4];

        // INSERT 쿼리
        $state = EnumState::RAW;
        $userId = EnumUser::ZENIMAX;
        $createdAt = "'" . date('Y-m-d H:i:s') . "'";
        $sql = "INSERT INTO `lang_id_unknown_index_offsets` (`lang_id`, `unknown`, `index`, `offset`, `text`, `state`, `user_id`, `created_at`, `updated_at`) 
    VALUES ($langId, $unknown, $index, $offset, :lang_text, $state, $userId, $createdAt, $createdAt)";

        // Prepared Statement 사용
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':lang_text', $text); // 특수 문자 안전하게 넣기 위해서 bind
        if ($stmt->execute() === false) {
            throw new Exception("insert fail, $langId, $unknown, $index, $offset");
        }

        // 진행 확인용 문구 출력한다.
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