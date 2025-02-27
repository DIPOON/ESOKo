<?php

namespace App\Enum;

/**
 * ESO 업데이트 단위
 */
class EnumPatch
{
    const NONE = 0;            // 이 상태이면 안됨. DB가 DEFAULT 값 넣은 것
    // ... 중간 생략
    const UPDATE_45_PTS = 450; // update-45pts lang 파일

    /**
     * patch 값을 사람이 읽을 수 있는 string 으로 변환합니다.
     */
    static array $patchName = array(
        self::NONE => 'None',
        self::UPDATE_45_PTS => '45 PTS',
    );
}
