<?php

require __DIR__ . '/../../WebProject/vendor/autoload.php';
require '../vendor/autoload.php';

use App\Common\Converter;

/**
 * lang_id_unknown_index_offsets 테이블에서 kr.lang 을 만든다.
 */
try {
    // 소요 시간 측정하기 위해서 시각 추가
    echo date('Y-m-d H:i:s') . "\n";

    // 새로 추가된 부분 조회
    $file = fopen("../Design/new.kr.lang.csv", 'w');
    if ($file === false) {
        throw new Exception("Unable to open file!");
    }

    // 첫번째 줄
    fwrite($file, '"ID","Unknown","Index","Offset","Text"' . PHP_EOL);

    // PDO 객체 생성
    $host = 'host.docker.internal';
    $dbname = 'laravel';
    $username = 'root';
    $password = 'korean@local';
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 번역, 변환해서 저장
    $counter = 0;
    $getCount = 0;
    do {
        // 일정 수량만큼 잘라서 가져옴
        $sql = "select * from `lang_id_unknown_index_offsets` limit $counter, 1000;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $getCount = count($result);
        $counter += $getCount;

        // 레코드마다 csv 파일에 추가
        foreach ($result as $row) {
            // 칼럼 확인
            if (isset($row['lang_id']) === false) {
                throw new Exception("no lang_id", implode("::", $row));
            }
            if (isset($row['unknown']) === false) {
                throw new Exception("no unknown", implode("::", $row));
            }
            if (isset($row['index']) === false) {
                throw new Exception("no index", implode("::", $row));
            }
            if (isset($row['offset']) === false) {
                throw new Exception("no offset", implode("::", $row));
            }
            if (isset($row['text']) === false) {
                throw new Exception("no text", implode("::", $row));
            }

            // 평이하게 입력
            $line[0] = $row['lang_id'];
            $line[1] = $row['unknown'];
            $line[2] = $row['index'];
            $line[3] = $row['offset'];

            // 필요한 내용 첨부하고 변환해서 입력
            $line[4] = Converter::convert_CN_KO($row['text']);

            // 입력
            fputcsv($file, $line);
        }

        // 진행 확인용 문구 출력한다.
        echo "$counter done";
        print("\n");
    } while ($getCount != 0);

    // 정리
    fclose($file);
    echo date('Y-m-d H:i:s') . "\n";

} catch (Exception $e) {
    echo $e->getCode();
    echo $e->getMessage();
}