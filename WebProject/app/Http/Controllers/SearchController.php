<?php

namespace App\Http\Controllers;

use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
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
     * @return RedirectResponse
     * @throws AuthenticationException
     * @throws Exception
     */
    public function getByText(Request $request): RedirectResponse
    {
        // 입력값 확인
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

        // request 정리
        $params = [
            'index' => 'my_index',
            'body' => [
                'query' => [
                    'match' => [
                        'testField' => $curiousText
                    ]
                ]
            ]
        ];

        // 엘라스틱서치에 문의
        try {
            $elasticResponse = $client->search($params);
            $arrayedResponse = $elasticResponse->asArray();
        } catch (Exception $e) {
            throw new Exception("fail to get elasticsearch\n" . $e->getMessage());
        }

        // 응답값 확인
        if (isset($arrayedResponse['hits']['hits']) === false) {
            throw new Exception("invalid arrayed response" . implode(":", $arrayedResponse));
        }

        return redirect()->route('search')
            ->with('curious_text', $curiousText)
            ->with('result_group', $arrayedResponse['hits']['hits']);
    }
}
