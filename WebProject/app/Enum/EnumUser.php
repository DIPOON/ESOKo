<?php

namespace App\Enum;

/**
 * 약속된 user id
 */
class EnumUser
{
    CONST NONE = 0;       // 이 상태이면 안됨. DB가 DEFAULT 값 넣은 것
    CONST ZENIMAX = 1;    // en.lang.csv 에서 뽑아서 넣은 그 상태
    CONST ADMIN = 2;      // 관리자
    CONST DISTRO = 3;     // 배포된 버전의 값
    CONST ANONYMOUS = 4; // 익명의 번역자
    // 100 ~ 실제 유저 발급
}
