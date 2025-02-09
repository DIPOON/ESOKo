<?php

namespace App\Enum;

/**
 * 번역 상태
 */
class EnumVersion
{
    CONST NONE = 0;            // 이 상태이면 안됨. DB가 DEFAULT 값 넣은 것
    // ... 중간 생략
    CONST UPDATE_45_PTS = 450; // update-45pts lang 파일
}
