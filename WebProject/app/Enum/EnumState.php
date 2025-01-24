<?php

namespace App\Enum;

/**
 * 번역 상태
 */
class EnumState
{
    CONST NONE = 0;     // 이 상태이면 안됨. DB가 DEFAULT 값 넣은 것
    CONST RAW = 10;     // en.lang.csv 에서 뽑아서 넣은 그 상태
    CONST ML = 20;      // 기계 번역된 상태
    CONST UNKNOWN = 30; // 익명의 번역자가 번역했음
    CONST PLAYER = 40;  // 엘온 플레이어가 번역
    CONST REVIEW = 50;  // 리뷰어가 번역했음
    CONST ADMIN = 60;   // 관리자 고정
}
