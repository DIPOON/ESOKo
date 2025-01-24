<?php

namespace App\Http\Controllers;

use App\Enum\EnumState;
use Illuminate\Support\Facades\DB;

class OriginalTextController extends Controller
{
    // 랜덤 질문 페이지 보여주기
    public function randomShow()
    {
        // lang_id-unknown-index-offset 테이블에서 랜덤하게 하나의 레코드 조회
        $stringed = '번역할 문장이 없나봐요';
        $result = DB::table('original_texts')
            ->where('state', EnumState::RAW) // TODO 나중에 번역 상태가 낮은 것부터 조회하도록 개선
            ->inRandomOrder()
            ->first();
        if (is_null($result) === false) {
            $stringed = $result->lang_text;
        }
        return view('translate', ['question' => $stringed]);
    }
}
