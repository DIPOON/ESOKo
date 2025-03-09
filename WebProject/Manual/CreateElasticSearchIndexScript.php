<?php

use Elastic\Elasticsearch\ClientBuilder;

require __DIR__ . '/../vendor/autoload.php';

/*
 * 문장으로 검색할 인덱스를 만드는 스크립트
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

    // 엘라스틱서치에 처리
    try {
        $params = [
            'index' => 'my_index',
            'id'    => 'my_id',
            'body'  => ['testField' => 'abc']
        ];
        $response = $client->index($params);
        print_r($response->asArray());
    } catch (Exception $e) {
        throw new Exception("fail to get elasticsearch\n" . $e->getMessage());
    }

    // 스크립트 종료 시각 확인
    echo date('Y-m-d H:i:s') . "\n";

} catch (Exception $e) {
    echo $e->getCode();
    echo $e->getMessage();
}
