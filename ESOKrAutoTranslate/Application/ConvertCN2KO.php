<?php

// 사용 환경이 다를 때 오류날까봐 확인
echo mb_internal_encoding(); // 결과 UTF-8
print("\n");

// utf-8 value 자유롭게 다루기
$beforeString = "가나다라";
$afterString = "";
for ($i = 0; $i < mb_strlen($beforeString, "utf-8"); $i++) { // beforeString 에서 한글자씩 convert
    // 한글자 utf-8 value
    $eachChar = mb_substr($beforeString, $i, 1, "utf-8");
    $beforeUTF8Value = hexdec("0x" . bin2hex($eachChar));

     // 맨 마지막 $resultCharValue >= 0xE6B880 and $resultCharValue <= 0xE9A6A3 영역 빼고는 한글 -> 한자
    if ($beforeUTF8Value >= 0xE18480 and $beforeUTF8Value <= 0xE187BF) {
        $resultCharValue = $beforeUTF8Value + 0x43400;
    } else if ($beforeUTF8Value > 0xE384B0 and $beforeUTF8Value <= 0xE384BF) {
        $resultCharValue = $beforeUTF8Value + 0x237D0;
    } else if ($beforeUTF8Value > 0xE38580 and $beforeUTF8Value <= 0xE3868F) {
        $resultCharValue = $beforeUTF8Value + 0x23710;
    } else if ($beforeUTF8Value >= 0xEAB080 and $beforeUTF8Value <= 0xED9EAC) {
        if ($beforeUTF8Value >= 0xEAB880 and $beforeUTF8Value <= 0xEABFBF) {
            $resultCharValue = $beforeUTF8Value - 0x33800;
        } else if ($beforeUTF8Value >= 0xEBB880 and $beforeUTF8Value <= 0xEBBFBF) {
            $resultCharValue = $beforeUTF8Value - 0x33800;
        } else if ($beforeUTF8Value >= 0xECB880 and $beforeUTF8Value <= 0xECBFBF) {
            $resultCharValue = $beforeUTF8Value - 0x33800;
        } else {
            $resultCharValue = $beforeUTF8Value - 0x3F800;
        }
    } else if ($beforeUTF8Value >= 0xE6B880 and $beforeUTF8Value <= 0xE9A6A3) {
        $resultCharValue = $beforeUTF8Value + 0x3F800;
    }
    $afterString .= hex2bin(dechex($resultCharValue));
}

echo $afterString;