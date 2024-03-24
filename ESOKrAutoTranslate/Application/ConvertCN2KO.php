<?php

// 사용 환경이 다를 때 오류날까봐 확인
echo mb_internal_encoding(); // 결과 UTF-8
print("\n");

// utf-8 value 자유롭게 다루기
$beforeString = "가나다라";
$afterString = "";
for ($eachChar = 0; $eachChar < mb_strlen($beforeString, "utf-8"); $eachChar++) { // beforeString 에서 한글자씩 convert
   $slicedChar = mb_substr($beforeString, $eachChar, 1, "utf-8");
   $hexContent = "";
   for ($j = 0; $j < strlen($slicedChar); $j++) {
       $splicedHexContent = dechex(ord($slicedChar[$j]));
       $hexContent = $hexContent . $splicedHexContent;
   }
   $resultCharValue = hexdec("0x" . $hexContent);

   // 맨 마지막 $resultCharValue >= 0xE6B880 and $resultCharValue <= 0xE9A6A3 영역 빼고는 한글 -> 한자
   if ($resultCharValue >= 0xE18480 and $resultCharValue <= 0xE187BF) {
       $resultCharValue = $resultCharValue + 0x43400;
   } else if ($resultCharValue > 0xE384B0 and $resultCharValue <= 0xE384BF) {
       $resultCharValue = $resultCharValue + 0x237D0;
   } else if ($resultCharValue > 0xE38580 and $resultCharValue <= 0xE3868F) {
       $resultCharValue = $resultCharValue + 0x23710;
   } else if ($resultCharValue >= 0xEAB080 and $resultCharValue <= 0xED9EAC) {
       if ($resultCharValue >= 0xEAB880 and $resultCharValue <= 0xEABFBF) {
           $resultCharValue = $resultCharValue - 0x33800;
       } else if ($resultCharValue >= 0xEBB880 and $resultCharValue <= 0xEBBFBF) {
           $resultCharValue = $resultCharValue - 0x33800;
       } else if ($resultCharValue >= 0xECB880 and $resultCharValue <= 0xECBFBF) {
           $resultCharValue = $resultCharValue - 0x33800;
       } else {
           $resultCharValue = $resultCharValue - 0x3F800;
       }
   } else if ($resultCharValue >= 0xE6B880 and $resultCharValue <= 0xE9A6A3) {
       $resultCharValue = $resultCharValue + 0x3F800;
   }
   $afterString .= hex2bin(dechex($resultCharValue));
}

echo $afterString;