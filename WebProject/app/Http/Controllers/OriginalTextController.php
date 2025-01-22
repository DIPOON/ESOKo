<?php

namespace App\Http\Controllers;

use App\Models\OriginalText;

class OriginalTextController extends Controller
{
    // 랜덤 질문 페이지 보여주기
    public function randomShow()
    {
        // TODO questions 테이블에서 랜덤하게 하나의 레코드를 가져옵니다.
        $stringed = '번역할 문장이 없나봐요';
        foreach (OriginalText::all() as $flight) {
            $stringed = $flight->text;
        }
        return view('translate', ['question' => $stringed]);
    }
}
