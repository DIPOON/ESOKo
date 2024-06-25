<?php
/*
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

require '../vendor/autoload.php';

use DeepL\Translator;
use Google\ApiCore\ApiException;
use Google\Cloud\Translate\V3\Client\TranslationServiceClient;
use Google\Cloud\Translate\V3\TranslateTextGlossaryConfig;
use Google\Cloud\Translate\V3\TranslateTextRequest;

/**
 * @param string $text
 * @param string $targetLanguage
 * @param string $sourceLanguage
 * @param string $projectId
 * @param string $glossaryId
 * @return string
 * @throws ApiException
 * @throws Exception
 */
function v3_translate_text_with_glossary(
    string $text,
    string $targetLanguage,
    string $sourceLanguage,
    string $projectId,
    string $glossaryId
): string {
    $translationServiceClient = new TranslationServiceClient();

    $glossaryPath = $translationServiceClient->glossaryName(
        $projectId,
        'us-central1',
        $glossaryId
    );
    $contents = [$text];
    $formattedParent = $translationServiceClient->locationName(
        $projectId,
        'us-central1'
    );
    $glossaryConfig = new TranslateTextGlossaryConfig();
    $glossaryConfig->setGlossary($glossaryPath);

    // Optional. Can be "text/plain" or "text/html".
    $mimeType = 'text/plain';

    try {
        $request = (new TranslateTextRequest())
            ->setContents($contents)
            ->setTargetLanguageCode($targetLanguage)
            ->setParent($formattedParent)
            ->setSourceLanguageCode($sourceLanguage)
            ->setGlossaryConfig($glossaryConfig)
            ->setMimeType($mimeType);
        $response = $translationServiceClient->translateText($request);
        // Display the translation for each input text provided
        foreach ($response->getGlossaryTranslations() as $translation) {
            return $translation->getTranslatedText();
        }
    } finally {
        $translationServiceClient->close();
    }
    throw new Exception("why come here");
}

/**
 * @param string $beforeString          The text to convert.
 * @return string converted text.
 */
function convert_CN_KO(string $beforeString): string {
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

try {
    // 소요 시간 측정하기 위해서 시각 추가
    echo time() . "\n";

    // 새로 추가된 부분 조회
    $file = fopen("../Design/update_42_added.csv", 'r');
    if ($file === false) {
        throw new Exception("Unable to open file!");
    }

    // 번역, 변환해서 저장
    $fp = fopen('real.csv', 'w');
    $counter = 0;
    while (($line = fgetcsv($file)) !== FALSE) {
        // 원문 Text 확인
        if (isset($line[4]) === false) {
            throw new Exception("line 4 is not set");
        }
        $originalString = $line[4];
        echo $originalString." "."\n";


        // 정상적인 문자일 때 번역한다. 번역 성공 시 한글이므로 한문 변환한다.
        if (is_string($originalString) === true) {
            // 구글 번역
//            $translatedString = v3_translate_text_with_glossary($originalString, "ko", "en",
//                project_name, "test_glossary_2");

            // 디플 번역
            $authKey = ""; // Replace with your key
            $translator = new Translator($authKey);
            $result = $translator->translateText($originalString, null, 'ko');
            $translatedString = $result->text;

            // 번역문으로 대체
            if (is_string($translatedString) === true) {
                echo $translatedString." ";
                print("\n");
                $convertedString = convert_CN_KO($translatedString);
                if (is_string($convertedString) === true) {
                    echo $convertedString." ";
                    print("\n");
                    $line[4] = $convertedString;
                }
            }
        }

        // 문자이든 아니든 데이터 추가한다.
        fputcsv($fp, $line);

        // 진행 확인용 문구 출력한다.
        $counter++;
        print($counter);
        print("\n");
        print("\n\n");
    }

    // 정리
    fclose($file);
    fclose($fp);
    echo time() . "\n";
    echo "와 이게 끝나네";

} catch (Exception $e) {
    echo $e->getCode();
    echo $e->getMessage();
    echo "나중에 구체적으로 에러 처리 추가하죠 뭐";
}