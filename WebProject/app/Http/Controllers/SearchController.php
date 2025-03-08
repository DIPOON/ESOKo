<?php

namespace App\Http\Controllers;

use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * @return View|Factory|Application
     * @throws Exception
     */
    public function get(): View|Factory|Application
    {
        return view('search', array('result_group' => array()));
    }

    /**
     * @param Request $request
     * @return View|Factory|Application
     * @throws AuthenticationException
     * @throws Exception
     */
    public function getByText(Request $request): View|Factory|Application
    {
        // 입력값 조회
        $validatedData = $request->validate([
            'curious_text' => 'required',
        ]);
        $curiousText = $validatedData['curious_text'];

        // 엘라스틱 서치에 접속합니다.
        $elasticSearchPassword = getenv('ELASTIC_SEARCH_PASSWORD');
        if ($elasticSearchPassword === false) {
            throw new Exception("no elasticsearch password");
        }
        $elasticSearchUsername = getenv('ELASTIC_SEARCH_USERNAME');
        if ($elasticSearchUsername === false) {
            throw new Exception("no elasticsearch username");
        }
        $client = ClientBuilder::create()
            ->setHosts(['https://elasticsearch-master:9200'])
            ->setBasicAuthentication($elasticSearchUsername, $elasticSearchPassword)
            ->setCABundle('/etc/secret-volume/ca.crt')
            ->build();

        // 엘라스틱서치에 문의
        $resultGroup = array();
        try {
            $response = $client->info();
        } catch (Exception $e) {
            throw new Exception("fail to get elasticsearch");
        }
//        $params = [
//            'index' => 'my_index',
//            'id'    => 'my_id',
//            'body'  => ['testField' => 'abc']
//        ];
//
//        $response = $client->index($params);
//        print_r($response->asArray());

        // 응답 정리
        $response = array(
            'result_group' => $resultGroup,
        );

        // JSON으로 반환
        return response()->json($response);
    }
}
