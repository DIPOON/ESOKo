<?php

require '../vendor/autoload.php';

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
 * kr.lang 을 lang_id_unknown_index_offsets 테이블에 넣는다.
 */
try {
    // 소요 시간 측정하기 위해서 시각 추가
    echo date('Y-m-d H:i:s') . "\n";

    // 새로 추가된 부분 조회
    $file = fopen("../Design/kr.lang.csv", 'r');
    if ($file === false) {
        throw new Exception("Unable to open file!");
    }
    $host = 'host.docker.internal';
    $dbname = 'laravel';
    $username = 'root';
    $password = 'korean@local';

    $check = convert_CN_KO('邠');

    // PDO 객체 생성
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 번역, 변환해서 저장
    $counter = 0;
    while (($line = fgetcsv($file)) !== FALSE) {
        // CSV 파일에서 데이터 조회
        $langId = $line[0];
        $unknown = $line[1];
        $index = $line[2];
        $offset = $line[3];
        $text = convert_CN_KO($line[4]);

        // INSERT 쿼리
        $state = 20; // 디렉토리 달라서 못찾길래 그냥
        $userId = 0;
        $createdAt = "'" . date('Y-m-d H:i:s') . "'";
        $sql = "INSERT INTO `lang_id_unknown_index_offsets` (`lang_id`, `unknown`, `index`, `offset`, `text`, `state`, `user_id`, `created_at`, `updated_at`) 
    VALUES ($langId, $unknown, $index, $offset, :lang_text, $state, $userId, $createdAt, $createdAt)";

        // Prepared Statement 사용
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':lang_text', $text);
        if ($stmt->execute() === false) {
            throw new Exception("insert fail, $langId, $unknown, $index, $offset");
        }

        // 진행 확인용 문구 출력한다.
        $counter++;
        echo "$counter, $langId, $unknown, $index, $offset done";
        print("\n");
    }

    // 정리
    fclose($file);
    echo date('Y-m-d H:i:s') . "\n";
    echo "와 이게 끝나네";

} catch (Exception $e) {
    echo $e->getCode();
    echo $e->getMessage();
}