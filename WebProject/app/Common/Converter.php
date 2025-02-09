<?php

namespace App\Common;

use Exception;

class Converter
{
    /**
     * @param string $beforeString The text to convert.
     * @return string converted text.
     */
    function convert_CN_KO(string $beforeString): string
    {
        $afterString = "";
        for ($i = 0; $i < mb_strlen($beforeString, "utf-8"); $i++) { // beforeString 에서 한글자씩 convert
            // 한글자 utf-8 value
            $eachChar = mb_substr($beforeString, $i, 1, "utf-8");
            $beforeUTF8Value = hexdec("0x" . bin2hex($eachChar));

            // 맨 마지막 $resultCharValue >= 0xE6B880 and $resultCharValue <= 0xE9A6A3 영역 빼고는 한글 -> 한자
            $resultCharValue = null;
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

            // 결과 string에 덧붙이기
            if (is_null($resultCharValue) === true) { // Convert 당할 글자를 제외하면 원본
                $afterString .= $eachChar;
            } else { // Convert 된 글자
                $afterString .= hex2bin(dechex($resultCharValue));
            }
        }
        return $afterString;
    }

    /**
     * 기존의 esokr.lua 에서의 문자 대응 convert_CN_KO 역함수. 해독용
     * @param string $beforeString
     * @return string
     * @throws Exception
     */
    static function reverseConvert_CN_Ko(string $beforeString): string
    {
        $afterString = "";
        for ($i = 0; $i < mb_strlen($beforeString, "utf-8"); $i++) { // beforeString 에서 한글자씩 convert
            // 한글자 utf-8 value
            $eachChar = mb_substr($beforeString, $i, 1, "utf-8");
            $beforeUTF8Value = hexdec("0x" . bin2hex($eachChar));

            // conver_CN_KO 쪽과 헷갈리지 않기 위해서 그냥 다 더함
            if ($beforeUTF8Value >= 0xEAB880 - 0x33800 && $beforeUTF8Value <= 0xEABFBF - 0x33800) {
                $resultCharValue = $beforeUTF8Value + 0x33800;
            } else if ($beforeUTF8Value >= 0xEBB880 - 0x33800 && $beforeUTF8Value <= 0xEBBFBF - 0x33800) {
                $resultCharValue = $beforeUTF8Value + 0x33800;
            } else if ($beforeUTF8Value >= 0xECB880 - 0x33800 && $beforeUTF8Value <= 0xECBFBF - 0x33800) {
                $resultCharValue = $beforeUTF8Value + 0x33800;
            } else if ($beforeUTF8Value >= 0x20 && $beforeUTF8Value <= 0x7F) { // 아스키 기본
                $resultCharValue = $beforeUTF8Value;
            } else if ($beforeUTF8Value >= 0xE6B880 and $beforeUTF8Value <= 0xE9A6A3) {
                $resultCharValue = $beforeUTF8Value + 0x3F800; // 두번째 if 절들은 위에서 처리했겠지...
            }

//            // 여기서부터는 그냥 검증해보려고. 여기서 걸리는 것들은 사실 번역 검수 필요하다고 봐야함
//            else if ($beforeUTF8Value == 0xE280A6) { // 말줄임표 …
//                $resultCharValue = $beforeUTF8Value;
//            } else if ($beforeUTF8Value == 14844052) { // 줄표 —
//                $resultCharValue = $beforeUTF8Value;
//            } else if ($beforeUTF8Value == 14910881) { // 한글 '으' ㅡ
//                $resultCharValue = $beforeUTF8Value;
//            } else if ($beforeUTF8Value == 15711361) { // ！
//                $resultCharValue = $beforeUTF8Value;
//            } else if ($beforeUTF8Value == 14909580) { // 「
//                $resultCharValue = $beforeUTF8Value;
//            } else if ($beforeUTF8Value == 14909581) { // 」
//                $resultCharValue = $beforeUTF8Value;
//            } else if ($beforeUTF8Value == 15047856) { // 地 어머니의 지악 이라는 문장에서 한문 표기
//                $resultCharValue = $beforeUTF8Value;
//            } else if ($beforeUTF8Value == 15106977) { // 惡 어머니의 지악 이라는 문장에서 한문 표기
//                $resultCharValue = $beforeUTF8Value;
//            } else if ($beforeUTF8Value == 14989485) { // 中 中装天上夢見人(芬篝)^m 이라는 문장에서 한문 표기
//                $resultCharValue = $beforeUTF8Value;
//            } else if ($beforeUTF8Value == 15049897) { // 天 中装天上夢見人(芬篝)^m 이라는 문장에서 한문 표기
//                $resultCharValue = $beforeUTF8Value;
//            } else if ($beforeUTF8Value == 14989450) { // 上 中装天上夢見人(芬篝)^m 이라는 문장에서 한문 표기
//                $resultCharValue = $beforeUTF8Value;
//            } else if ($beforeUTF8Value == 15049890) { // 夢 中装天上夢見人(芬篝)^m 이라는 문장에서 한문 표기
//                $resultCharValue = $beforeUTF8Value;
//            } else if ($beforeUTF8Value == 14990010) { // 人 中装天上夢見人(芬篝)^m 이라는 문장에서 한문 표기
//                $resultCharValue = $beforeUTF8Value;
//            } else if ($beforeUTF8Value == 14990739) { // 体 神体(蓠躴) 이라는 문장에서 한문 표기
//                $resultCharValue = $beforeUTF8Value;
//            } else if ($beforeUTF8Value == 15312831) { // 駿 靠穜袰覭襘 蠁顔駿馬^ 이라는 문장에서 한문 표기
//                $resultCharValue = $beforeUTF8Value;
//            } else if ($beforeUTF8Value == 15312556) { // 馬 靠穜袰覭襘 蠁顔駿馬^ 이라는 문장에서 한문 표기
//                $resultCharValue = $beforeUTF8Value;
//            } else if ($beforeUTF8Value == 14910395) { // ・ 盱縛襴 靘遘・粨鋀 이라는 문장에서 한문 표기. 돌 등받이 하치
//                $resultCharValue = $beforeUTF8Value;
//            } else if ($beforeUTF8Value == 49847) { // · 計潬·茸躙^F 이라는 문장에서 한문 표기. 돌 등받이 하치
//                $resultCharValue = $beforeUTF8Value;
//            } else if ($beforeUTF8Value == 14910358) { // ブ ブル・猤遘^ 이라는 문장에서 한문 표기. 돌 등받이 하치
//                $resultCharValue = $beforeUTF8Value;
//            } else if ($beforeUTF8Value == 14910379) { // ル ブル・猤遘^ 이라는 문장에서 한문 표기. 돌 등받이 하치
//                $resultCharValue = $beforeUTF8Value;
//            } else if ($eachChar == '浪') {
//                $resultCharValue = $beforeUTF8Value;
//            } else if ($eachChar == '費') {
//                $resultCharValue = $beforeUTF8Value;
//            } else if ($eachChar == '家') {
//                $resultCharValue = $beforeUTF8Value;
//            } else if ($eachChar == '打') { // 猛打者(篹鋀覐)
//                $resultCharValue = $beforeUTF8Value;
//            } else if ($eachChar == '棍') { // 棍
//                $resultCharValue = $beforeUTF8Value;
//            }

//            else { // 일반적인 한자를 쓰는 분, 특수 기호, 일본어 등의 문자가 번역된 문장에 있어서 전부 예외처리할 수는 없을듯
//                throw new Exception("what is $beforeUTF8Value, $eachChar, $beforeString, $afterString");
//            }
            else {
                $resultCharValue = $beforeUTF8Value;
            }

            // 결과 string에 덧붙이기
            $afterString .= hex2bin(dechex($resultCharValue));
        }
        return $afterString;
    }
}
