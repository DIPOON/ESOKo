# ESOKo

엘더스크롤 온라인 한글 패치를 작업하기 위해서 부차적으로 사용했던 함수를 저장했습니다.

## ESOKo 프로젝트 단계
1. https://esofiles.uesp.net/ 업데이트 41 en.lang 파일을 여기서 구한다
<br>
2. Esoextractdata -d 옵션으로 kr.lang 이랑 en.lang 차이를 구한다
<br>
3. Esoextractdata -l 옵션으로 kr.lang.csv 만들어서 en.lang에는 있고 kr.lang에는 없는 행을 추가한다
<br>
  4. Application/Translate.php 로 구글 번역한다
  <br>
~~5. kr.lang.csv 에서 백슬래쉬로 줄이 잘리지 않게 가공한다
https://github.com/DIPOON/ESOKRSub/blob/main/BackslashQuotationRemover.py
<br>
이유는 잘 모르겠는데 이부분 하니까 문자열이 오히려 잘림~~
<br>
6. esoextractdata -x 옵션으로 kr Lang 파일 만든다
<br>
7. 완성된 kr.lang 파일을 기존 에드온 파일 gamedata 쪽에서 갈아끼운다
<br>

## TODO List
esokr 코드 정리
<br>
유저 참여 가능한 번역툴