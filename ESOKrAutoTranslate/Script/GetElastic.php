<?php

require __DIR__ . '/../../WebProject/vendor/autoload.php';

use Elastic\Elasticsearch\ClientBuilder;

try {
    // 스크립트 시작 시각 확인
    echo date('Y-m-d H:i:s') . "\n";

    // 연결
    $client = ClientBuilder::create()
        ->setHosts(['https://host.docker.internal:9200'])
        ->setBasicAuthentication('elastic', "비밀번호") // TODO 실제 비밀 번호 필요
        ->setSSLVerification(false)
        ->build();


    $params = [
        'index' => 'my_index',
        'id' => '204987124-0-75383-en',
    ];
    $response = $client->get($params);

    $info = $response->asArray();
    print_r($info);

    // 스크립트 종료 시각 확인
    echo date('Y-m-d H:i:s') . "\n";

} catch (Exception $e) {
    echo $e->getCode();
    echo $e->getMessage();
}
