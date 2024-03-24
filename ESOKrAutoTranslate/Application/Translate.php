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

use Google\Cloud\Translate\V3\Client\TranslationServiceClient;
use Google\Cloud\Translate\V3\TranslateTextGlossaryConfig;
use Google\Cloud\Translate\V3\TranslateTextRequest;

/**
 * @param string $text          The text to translate.
 * @param string $targetLanguage    Language to translate to.
 * @param string $sourceLanguage    Language of the source.
 * @param string $projectId     Your Google Cloud project ID.
 * @param string $glossaryId    Your glossary ID.
 * @return string translated text.
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
}

/**
 * @param string $beforeString          The text to covnert.
 * @return string converted text.
 */
function ConvertCN2KO(string $beforeString): string {
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
    $file = fopen("../Design/update_41_added.csv", 'r');
    $fp = fopen('real.csv', 'w');
    $counter = 0;
    while (($line = fgetcsv($file)) !== FALSE) {
        $counter++;
        if (isset($line[4]) === false) {
            throw new Exception("line 4 is not set");
        }
        $originalString = $line[4];
        echo $originalString." ";
        print("\n");
        if (is_string($originalString) === true) {
            $translatedString = v3_translate_text_with_glossary($originalString, "ko", "en", "horizontal-cab-417404", "test_glossary_2");
            if (is_string($translatedString) === true) {
                echo $translatedString." ";
                print("\n");
                $convertedString = ConvertCN2KO($translatedString);
                if (is_string($convertedString) === true) {
                    echo $convertedString." ";
                    print("\n");
                    $line[4] = (string)$convertedString;
                }
            }
        }
        fputcsv($fp, $line);
        print($counter);
        print("\n");
        print("\n\n");
    }
    fclose($file);
    fclose($fp);

    echo "와 이게 끝나네";

} catch (Exception $e) {
    echo "나중에 구체적으로 에러 처리 추가하죠 뭐";
}