<?php
try {
    // 소요 시간 측정하기 위해서 시각 추가
    echo time() . "\n";

    // 새로 추가된 부분 조회
    $file = fopen("../Design/kr_update_42_2.csv", 'r');
    if ($file === false) {
        throw new Exception("Unable to open file!");
    }

    // 빈 부분 찾아서 출력
    while (($line = fgetcsv($file)) !== FALSE) {
        if (isset($line[4]) === false) {
            throw new Exception("line 4 is not set");
        }
        $originalString = $line[4];

        if ($line[1] == 21337012
            && $line[2] == 0
            && $line[3] == 8023) {
            echo $originalString . "\n";
            break;
        }
    }

    // 정리
    fclose($file);
    echo time() . "\n";
    echo "와 이게 끝나네";

} catch (Exception $e) {
    echo $e->getCode();
    echo $e->getMessage();
    echo "나중에 구체적으로 에러 처리 추가하죠 뭐";
}