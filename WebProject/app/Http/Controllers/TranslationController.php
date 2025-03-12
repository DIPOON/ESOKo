<?php

namespace App\Http\Controllers;

use App\Common\ElasticManager;
use App\Enum\EnumState;
use App\Enum\EnumUser;
use App\Enum\EnumPatch;
use App\Models\TranslationLog;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TranslationController extends Controller
{
    /**
     * @return array
     */
    private function getTranslateDefaultReturn(): array
    {
        // lang_id-unknown-index-offset 테이블에서 랜덤하게 하나의 레코드 조회
        $stringed = '번역할 문장이 없나봐요';
        $langId = 0;
        $unknown = 0;
        $index = 0;
        $offset = 0;
        $result = DB::table('lang_id_unknown_index_offsets')
            ->where('state', EnumState::RAW) // TODO 나중에 번역 상태가 낮은 것부터 조회하도록 개선
            ->inRandomOrder()
            ->first();
        if (is_null($result) === false) {
            $stringed = $result->text;
            $langId = $result->lang_id;
            $unknown = $result->unknown;
            $index = $result->index;
            $offset = $result->offset;
        }
        return array(
            'question' => $stringed,
            'lang_id' => $langId,
            'unknown' => $unknown,
            'index' => $index,
            'offset' => $offset,
            'version' => EnumPatch::CURRENT_VERSION,
        );
    }

    /**
     * 랜덤 질문 페이지 보여주기
     * @return Application|Factory|View
     */
    public function randomShow(): Factory|View|Application
    {
        $result = self::getTranslateDefaultReturn();
        return view('translate', $result);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws AuthenticationException
     * @throws Exception
     */
    public function submit(Request $request): RedirectResponse
    {
        // 번역자 확인
        if (Auth::check() === true) {
            $userId = Auth::id();
        } else {
            $userId = $request->input('user_id', EnumUser::ANONYMOUS);
        }

        // 입력값 조회
        $validatedData = $request->validate([
            'lang_id' => 'required',
            'unknown' => 'required',
            'index' => 'required',
            'offset' => 'required',
            'answer' => 'required',
            'version' => 'required',
        ]);
        $langId = $validatedData['lang_id'];
        $unknown = $validatedData['unknown'];
        $index = $validatedData['index'];
        $offset = $validatedData['offset'];
        $text = $validatedData['answer'];
        $version = $validatedData['version'];

        // 번역문 저장
        DB::table('lang_id_unknown_index_offsets')
            ->where('lang_id', $langId)
            ->where('unknown', $unknown)
            ->where('index', $index)
            ->where('offset', $offset)
            ->update([
                'text' => $text,
                'state' => EnumState::UNKNOWN, // TODO 사용자 레벨에 따라 변경
                'user_id' => $userId,
            ]);

        // 번역 로그 남기기
        $translationLog = new TranslationLog();
        $translationLog->lang_id = $langId;
        $translationLog->unknown = $unknown;
        $translationLog->index = $index;
        $translationLog->offset = $offset;
        $translationLog->text = $text;
        $translationLog->version = $version;
        $translationLog->state = EnumState::UNKNOWN; // TODO 유저 레벨 반영
        $translationLog->user_id = $userId;
        $translationLog->save();

        // 엘라스틱 서치에 접속합니다.
        $client = ElasticManager::get();

        // 평이하게 입력
        $elasticId = $langId . '-' . $unknown . '-' . $index . '-kr';
        $krText = $text;
        $body = json_encode(array('content' => $krText));

        $params = [
            'index' => 'my_index',
            'id'    => $elasticId,
            'body'  => $body,
        ];

        // 엘라스틱서치에 등록
        try {
            $client->index($params);
        } catch (Exception $e) {
            throw new Exception("fail to get elasticsearch\n" . $e->getMessage());
        }

        return redirect()->route('translate')->with('message', $translationLog->id);
    }

    /**
     * 번역에 도움이될 정보 전달
     * @param Request $request
     * @return JsonResponse
     */
    public function getSub(Request $request): JsonResponse
    {
        // 입력값 조회
        $validatedData = $request->validate([
            'lang_id' => 'required',
            'unknown' => 'required',
            'index' => 'required',
        ]);
        $langId = $validatedData['lang_id'];
        $unknown = $validatedData['unknown'];
        $index = $validatedData['index'];

        // 번역 로그 조회
        $historyGroup = DB::table('translation_logs')
            ->where('lang_id', $langId)
            ->where('unknown', $unknown)
            ->where('index', $index)
            ->orderByDesc('id')
            ->limit(3)
            ->get();
        $historyList = array();
        foreach($historyGroup as $history) {
            $historyList[] = array(
                'text' => $history->text,
            );
        }

        // 앞뒤로 3개씩 확인
        $neighborIndexList = array(
            $index - 3,
            $index - 2,
            $index - 1,
            $index,
            $index + 1,
            $index + 2,
            $index + 3,
        );

        // 주변 문장 조회
        $neighborGroup = DB::table('lang_id_unknown_index_offsets')
            ->where('lang_id', $langId)
            ->where('unknown', $unknown)
            ->whereIn('index', $neighborIndexList)
            ->limit(7)
            ->get();
        $neighborList = array();
        foreach($neighborGroup as $neighbor) {
            $neighborList[] = array(
                'lang_id' => $neighbor->lang_id,
                'unknown' => $neighbor->unknown,
                'index' => $neighbor->index,
                'text' => $neighbor->text,
            );
        }

        // 응답 정리
        $response = array(
            'history_list' => $historyList,
            'neighbor_list' => $neighborList,
        );

        // JSON으로 반환
        return response()->json($response);
    }
}
