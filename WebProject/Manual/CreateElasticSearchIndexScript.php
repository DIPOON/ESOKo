<?php

use Elastic\Elasticsearch\ClientBuilder;

require __DIR__ . '/../vendor/autoload.php';

/*
 * lang_id_unknown_index_offset 테이블에서 읽어서 엘라스틱 서치에 등록한다.
 */
try {
    // 스크립트 시작 시각 확인
    echo date('Y-m-d H:i:s') . "\n";

    // 엘라스틱 서치에 접속합니다.
    $elasticSearchUsername = getenv('ELASTIC_SEARCH_USERNAME');
    if ($elasticSearchUsername === false) {
        throw new Exception("no elasticsearch username");
    }
    $elasticSearchPassword = getenv('ELASTIC_SEARCH_PASSWORD');
    if ($elasticSearchPassword === false) {
        throw new Exception("no elasticsearch password");
    }
    $client = ClientBuilder::create()
        ->setHosts(['https://elasticsearch-master:9200'])
        ->setBasicAuthentication($elasticSearchUsername, $elasticSearchPassword)
        ->setCABundle('/etc/secret-volume/ca.crt')
        ->build();

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

        // 레코드마다 엘라스틱서치 검색용 인덱스에 추가
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
            $elasticId = $row['lang_id'] . '-' . $row['unknown'] . '-' . $row['index'] . '-kr';
            $krText = $row['text'];
            $body = json_encode(array('content' => $krText));

            $params = [
                'index' => 'my_index',
                'id'    => $elasticId,
                'body'  => $body,
            ];

            // 엘라스틱서치에 등록
            try {
                $response = $client->index($params);
                print_r($response->asArray());
            } catch (Exception $e) {
                throw new Exception("fail to get elasticsearch\n" . $e->getMessage());
            }
        }

        // 진행 확인용 문구 출력한다.
        echo "$counter done";
        print("\n");
    } while ($getCount != 0);

    // 스크립트 종료 시각 확인
    echo date('Y-m-d H:i:s') . "\n";

} catch (Exception $e) {
    echo $e->getCode();
    echo $e->getMessage();
}
