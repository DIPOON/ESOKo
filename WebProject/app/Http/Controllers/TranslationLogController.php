<?php

namespace App\Http\Controllers;

use App\Models\OriginalText;

class TranslationLogController extends Controller
{
    public function insert()
    {
        // TODO 번역문 남기기
        return view('translate', ['question' => 'done']);
    }
}
