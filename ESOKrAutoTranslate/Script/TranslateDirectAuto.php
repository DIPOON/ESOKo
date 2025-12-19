<?php
// 비동기 시그널 처리
declare(ticks=1);

$stopRequested = false;

// 시그널 핸들러 등록 (Ctrl+C나 kill 명령을 감지함)
pcntl_signal(SIGTERM, function () use (&$stopRequested) {
    echo "\n🛑 종료 신호 감지! 현재 작업만 마치고 종료합니다...\n";
    $stopRequested = true;
});
pcntl_signal(SIGINT, function () use (&$stopRequested) {
    echo "\n🛑 종료 신호(Ctrl+C) 감지! 현재 작업만 마치고 종료합니다...\n";
    $stopRequested = true;
});

require __DIR__ . '/../../WebProject/vendor/autoload.php';
require '../vendor/autoload.php';

use App\Enum\EnumState;
use App\Enum\EnumUser;
use Elastic\Elasticsearch\ClientBuilder;
use Google\ApiCore\ApiException;
use Google\Cloud\Translate\V3\Client\TranslationServiceClient;
use Google\Cloud\Translate\V3\TranslateTextGlossaryConfig;
use Google\Cloud\Translate\V3\TranslateTextRequest;

// ==========================================
// [설정] 환경에 맞게 수정해주세요
// ==========================================
// MySQL
$dbHost = ''; // 혹은 DB IP
$dbName = ''; // 실제 DB 이름
$dbUser = ''; // DB 유저
$dbPass = ''; // DB 비밀번호

// Elasticsearch
$esHost = '';
$esUser = '';
$esPass = '';

$projectId = ''; // GCP 프로젝트 ID
$glossaryId = ''; // 단어장 ID (고유값)
// ==========================================

try {
    // 소요 시간 측정하기 위해서 시각 추가
    echo date('Y-m-d H:i:s') . "\n";

    // 엘라스틱 서치에 접속합니다.
    $client = ClientBuilder::create()
        ->setHosts([$esHost])
        ->setBasicAuthentication($esUser, $esPass)
        ->setSSLVerification(false)
        ->build();

    // PDO 객체 생성
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 번역, 변환해서 저장
    $recordCounter = 0;
    $startCounter = 0;
    $getCount = 0;
    do {
        // 루프 탈출 -> 스크립트 정상 종료
        pcntl_signal_dispatch();
        if ($stopRequested) {
            echo "✅ 안전하게 종료되었습니다.\n";
            break;
        }

        // 일정 수량만큼 잘라서 가져옴
        $sql = "select * from `lang_id_unknown_index_offsets` WHERE `state` = 10 limit 1000;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $getCount = count($result);
        $startCounter += $getCount;

        // TranslationController submit 처리처럼 번역 수행
        $request = (new TranslateTextRequest());
        foreach ($result as $row) {
            // 칼럼 확인
            if (isset($row['lang_id']) === false) {
                throw new Exception("no lang_id", implode("::", $row));
            }
            $langId = $row['lang_id'];
            if (isset($row['unknown']) === false) {
                throw new Exception("no unknown", implode("::", $row));
            }
            $unknown = $row['unknown'];
            if (isset($row['index']) === false) {
                throw new Exception("no index", implode("::", $row));
            }
            $index = $row['index'];
            if (isset($row['text']) === false) {
                throw new Exception("no text", implode("::", $row));
            }
            $originalString = $row['text'];

            // 구글 번역
            $translatedString = v3_translate_text_with_glossary($originalString, "ko", "en",
                $projectId, $glossaryId, $request);

            // 번역 로그 남기기. 로그는 여러개 남겨도 돼서 먼저 처리
            $state = EnumState::ML;
            $userId = EnumUser::GOOGLE_TRANSLATOR;
            $createdAt = "'" . date('Y-m-d H:i:s') . "'";
            $sql = "INSERT INTO `translation_logs` (`lang_id`, `unknown`, `index`, `offset`, `text`, `version`, `state`, `user_id`, `created_at`, `updated_at`) 
    VALUES ($langId, $unknown, $index, 7, :lang_text, 485, $state, $userId, $createdAt, $createdAt)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':lang_text', $translatedString); // 특수 문자 안전하게 넣기 위해서 bind
            if ($stmt->execute() === false) {
                throw new Exception("insert fail, $langId, $unknown, $index");
            }

            // 엘라스틱서치에 등록
            $elasticId = $langId . '-' . $unknown . '-' . $index . '-kr';
            try {
                $response = $client->index([
                    'index' => 'my_index',
                    'id' => $elasticId,
                    'body' => json_encode(array('content' => $translatedString)),
                ]);
            } catch (Exception $e) {
                throw new Exception("fail to get elasticsearch\n" . $e->getMessage());
            }

            // lang_id_unknown_index 테이블 기준으로 처리 여부를 결정하기에 가장 마지막에 UPDATE 쿼리
            $sql = "
          UPDATE `lang_id_unknown_index_offsets`
          SET 
              `text` = :lang_text,
              `user_id` = $userId,
              `state` = $state,
              updated_at = $createdAt
          WHERE
            lang_id = $langId
            AND `unknown` = $unknown
            AND `index` = $index;";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':lang_text', $translatedString); // 특수 문자 안전하게 넣기 위해서 bind
            if ($stmt->execute() === false) {
                throw new Exception("update fail, $langId, $unknown, $index");
            }

            // 진행 확인용 문구 출력한다.
            $recordCounter += 1;
            echo "$recordCounter, $langId, $unknown, $index, $translatedString\n";
        }

        // 진행 확인용 문구 출력한다.
        echo "$recordCounter";
        print("\n");
    } while ($getCount != 0);

    // 스크립트 종료 시각 확인
    echo date('Y-m-d H:i:s') . "\n";

} catch (Exception $e) {
    echo $e->getCode() . PHP_EOL;
    echo $e->getMessage() . PHP_EOL;
    echo date('Y-m-d H:i:s') . "\n";
}

/**
 * @param string $text
 * @param string $targetLanguage
 * @param string $sourceLanguage
 * @param string $projectId
 * @param string $glossaryId
 * @param TranslateTextRequest $request
 * @return string
 * @throws ApiException
 * @throws Exception
 */
function v3_translate_text_with_glossary(
    string               $text,
    string               $targetLanguage,
    string               $sourceLanguage,
    string               $projectId,
    string               $glossaryId,
    TranslateTextRequest $request,
): string
{
    $translationServiceClient = new TranslationServiceClient();

    $glossaryPath = $translationServiceClient->glossaryName(
        $projectId,
        'us-central1',
        $glossaryId
    );
    $formattedParent = $translationServiceClient->locationName(
        $projectId,
        'us-central1'
    );
    $glossaryConfig = new TranslateTextGlossaryConfig();
    $glossaryConfig->setGlossary($glossaryPath);
    $glossaryConfig->setIgnoreCase(true); // 단어장에 대소문자를 정규화 했음

    // 1. [전처리] 변수 패턴(<<...>>)을 찾아서 보호 태그로 감싸기
    // 정규식 설명: '<<'로 시작하고, 중간에 아무 문자나 있고, '>>'로 끝나는 덩어리
    $protectedText = preg_replace_callback(
        '/(<<[^>]+>>)/',
        function ($matches) {
            // 핵심: htmlspecialchars로 특수문자(<, >)를 안전한 형태(&lt;, &gt;)로 바꿈
            return '<span translate="no">' . htmlspecialchars($matches[0]) . '</span>';
        },
        $text
    );

    // 2. [요청] HTML 모드로 설정 (필수!)
    $mimeType = 'text/html'; // text/plain 아님!

    // ... (구글 API 요청 부분은 동일) ...
    try {
        $request->setContents([$protectedText])
            ->setTargetLanguageCode($targetLanguage)
            ->setParent($formattedParent)
            ->setSourceLanguageCode($sourceLanguage)
            ->setGlossaryConfig($glossaryConfig)
            ->setMimeType($mimeType);
        $response = $translationServiceClient->translateText($request);

        // 3. [후처리] 결과에서 HTML 태그 제거
        foreach ($response->getGlossaryTranslations() as $translation) {
            $translatedRaw = $translation->getTranslatedText();

            // <span> 태그 벗겨내기
            // html_entity_decode는 혹시 구글이 특수문자를 엔티티로 바꿨을 경우를 대비함
            return html_entity_decode(strip_tags($translatedRaw));
        }
    } finally {
        $translationServiceClient->close();
    }

    throw new Exception("invalid coming");
}
