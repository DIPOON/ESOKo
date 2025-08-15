<?php

namespace App\Enum;

/**
 * ESO 업데이트 단위
 */
class EnumPatch
{
    // 현재 사이트가 다루고 있는 업데이트 버전
    const CURRENT_VERSION = 475; // TODO 새로운 버전 텍스트 올릴 때마다 올려야함

    // 업데이트 구간. 예를 들어 46 업데이트는 460~469 까지를 사용하고 있음
    const VERSION_WIDTH = 10;

    // 일의 자리 구간 지정된 의미
    const TYPE_PTS = 0;    // 450, 460 은 PTS 버전 의미
    const TYPE_NORMAL = 5; // 455, 465 은 정식 출시 버전을 의미

    // 정의된 값
    const NONE = 0;   // 이 상태이면 안됨. DB가 DEFAULT 값 넣은 것
    const TEMP = 5;   // 급히 올릴 때 이름 대응 안되어 있을까봐
    const DISTRO = 6; // 이전 한패에 있던 버전. 무슨 버전인지 알 수 없어서 임시로 세팅한 값

    /**
     * @var array|string[] 규칙없이 미리 지정된 패치 이름
     */
    static array $predefinedPatchName = array(
        self::NONE => 'None',
        self::TEMP => '임시 파일',
    );


    /**
     * patch 값을 사람이 읽을 수 있는 string 으로 변환합니다.
     * @param int $patch
     * @return string
     */
    static public function getPatchName(int $patch): string
    {
        // 따로 규칙 없이 $patch 값에 따라 지정된 이름을 사용하는 경우
        if (isset(self::$predefinedPatchName[$patch])) {
            return self::$predefinedPatchName[$patch];
        }

        // 일의 자리는 업데이트 분류
        $type = $patch % self::VERSION_WIDTH;
        $update = intdiv($patch, self::VERSION_WIDTH);
        if ($type  === self::TYPE_PTS) {
            return "$update PTS";
        } else if ($type  === self::TYPE_NORMAL) {
            return "$update 본섭";
        }

        return '알 수 없는 버전';
    }
}
