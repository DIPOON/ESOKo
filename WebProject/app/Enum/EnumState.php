<?php

namespace App\Enum;

/**
 * 번역 상태
 */
class EnumState
{
    CONST NONE = 0;     // 이 상태이면 안됨. DB가 DEFAULT 값 넣은 것
    CONST RAW = 10;     // en.lang.csv 에서 뽑아서 넣은 그 상태
    CONST DISTRO = 20;  // kr.lang 에서 뽑은 것인데, RAW 일 수도 있고 ML일 수도 있음
    CONST ML = 30;      // 기계 번역된 상태
    CONST UNKNOWN = 40; // 익명의 번역자가 번역했음
    CONST PLAYER = 50;  // 엘온 플레이어가 번역
    CONST REVIEW = 60;  // 리뷰어가 번역했음
    CONST ADMIN = 70;   // 관리자 고정
}
