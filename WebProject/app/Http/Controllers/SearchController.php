<?php

namespace App\Http\Controllers;

use App\Common\ElasticManager;
use App\Enum\EnumPatch;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $client = ElasticManager::get();

        // request 정리
        $params = [
            'index' => 'my_index',
            'body' => [
                'query' => [
                    'match' => [
                        'content' => [
                            'query' => $curiousText,
//                            'fuzziness' => 'AUTO'
                        ]
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

    /**
     * @param Request $request
     * @return Factory|View|Application
     * @throws Exception
     */
    public function goTranslate(Request $request): Factory|View|Application
    {
        // 입력값 확인
        $validatedData = $request->validate([
            'result_id' => 'required',
        ]);
        $resultId = $validatedData['result_id'];
        $explodedResultId = explode('-', $resultId);
        $langId = $explodedResultId[0];
        $unknown = $explodedResultId[1];
        $index = $explodedResultId[2];

        // lang_id-unknown-index-offset 테이블에서 조회
        $result = DB::table('lang_id_unknown_index_offsets')
            ->where('lang_id', $langId)
            ->where('unknown', $unknown)
            ->where('index', $index)
            ->first();
        if (is_null($result)) {
            throw new Exception("invalid index $index");
        }

        // 번역 페이지로 요청한 검색 결과로 응답
        $result = array(
            'question' => $result->text,
            'lang_id' => $result->lang_id,
            'unknown' => $result->unknown,
            'index' => $result->index,
            'offset' => $result->offset,
            'version' => EnumPatch::CURRENT_VERSION,
        );
        return view('translate', $result);
    }
}
