<?php

require __DIR__ . '/../../WebProject/vendor/autoload.php';
require '../vendor/autoload.php';

use App\Enum\EnumState;
use App\Enum\EnumUser;
use Elastic\Elasticsearch\ClientBuilder;

/**
 * 새로운 en.lang 의 added 를 lang_id_unknown_index_offsets 테이블에 넣는다.
 */
try {
    // 소요 시간 측정하기 위해서 시각 추가
    echo date('Y-m-d H:i:s') . "\n";

    // 새로 추가된 부분 조회
    $file = fopen("../Design/en46.lang.changed.csv", 'r');
    if ($file === false) {
        throw new Exception("Unable to open file!");
    }
    $host = 'host.docker.internal';
    $dbname = 'laravel';
    $username = 'root';
    $password = 'korean@local'; // TODO 실제 비밀 번호 필요

    // PDO 객체 생성
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password); // TODO 실제 포트 필요 ;port=33066
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 엘라스틱서치 연결
    $client = ClientBuilder::create()
        ->setHosts(['https://host.docker.internal:9200'])
        ->setBasicAuthentication('elastic', "비밀번호") // TODO 실제 비밀 번호 필요
        ->setSSLVerification(false)
        ->build();

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

        // UPDATE 쿼리
        $state = EnumState::RAW;
        $userId = EnumUser::ZENIMAX;
        $createdAt = "'" . date('Y-m-d H:i:s') . "'";
        $sql = "
          UPDATE `lang_id_unknown_index_offsets`
          SET en_text = :lang_text,
              `text` = :lang_text,
              updated_at = '2025-06-18 06:22:23'
          WHERE
            lang_id = $langId
            AND `unknown` = $unknown
            AND `index` = $index;";

        // Prepared Statement 사용
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':lang_text', $text); // 특수 문자 안전하게 넣기 위해서 bind
        if ($stmt->execute() === false) {
            throw new Exception("insert fail, $langId, $unknown, $index, $offset");
        }

        // 평이하게 입력
        $elasticId = $langId . '-' . $unknown . '-' . $index . '-en';
        $body = json_encode(array('content' => $text));

        $params = [
            'index' => 'my_index',
            'id'    => $elasticId,
            'body'  => $body,
        ];

        // 엘라스틱서치에 등록
        try {
            $response = $client->index($params);
        } catch (Exception $e) {
            throw new Exception("fail to get elasticsearch\n" . $e->getMessage());
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