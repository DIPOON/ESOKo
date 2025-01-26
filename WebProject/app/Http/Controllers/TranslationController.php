<?php

namespace App\Http\Controllers;

use App\Enum\EnumState;
use App\Models\TranslationLog;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
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
        $version = 0;
        $result = DB::table('original_texts')
            ->where('state', EnumState::RAW) // TODO 나중에 번역 상태가 낮은 것부터 조회하도록 개선
            ->inRandomOrder()
            ->first();
        if (is_null($result) === false) {
            $stringed = $result->lang_text;
            $langId = $result->lang_id;
            $unknown = $result->unknown;
            $index = $result->index;
            $offset = $result->offset;
            $version = $result->version;
        }
        return array(
            'question' => $stringed,
            'lang_id' => $langId,
            'unknown' => $unknown,
            'index' => $index,
            'offset' => $offset,
            'version' => $version,
        );
    }

    /**
     * 랜덤 질문 페이지 보여주기
     * @return Application|Factory|View
     */
    public function randomShow()
    {
        $result = self::getTranslateDefaultReturn();
        return view('translate', $result);
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function submit(Request $request)
    {
        // 번역자 확인
        if (Auth::check() === true) {
            $userId = Auth::id();
        } else {
            $userId = $request->input('user_id', 0);
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

        // 번역문 남기기
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

        // 응답값 생성
        $result = self::getTranslateDefaultReturn();
        $result['message'] = $translationLog->id . ' done';

        return view('translate', $result);
    }
}
