<?php

namespace App\Common;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Exception;

class ElasticManager
{
    private static Client $elasticClient;

    /**
     * 엘라스틱 서치에 접속합니다.
     * @return Client
     * @throws AuthenticationException
     * @throws Exception
     */
    static function get(): Client
    {
        // 이미 연결되어 있으면 해당 클라이언트 객체를 사용합니다.
        if (isset(self::$elasticClient)) {
            return self::$elasticClient;
        }

        // 엘라스틱 비밀번호 조회
        $elasticSearchPassword = getenv('ELASTIC_SEARCH_PASSWORD');
        if ($elasticSearchPassword === false) {
            throw new Exception("no elasticsearch password");
        }

        // 연결
        self::$elasticClient = ClientBuilder::create()
            ->setHosts(['https://quickstart-es-http:9200'])
            ->setBasicAuthentication('elastic', $elasticSearchPassword)
            ->setCABundle('/etc/secret-volume/ca.crt')
            ->build();

        // 연결된 객체 반환
        return self::$elasticClient;
    }
}
