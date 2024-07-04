# ESOKo

엘더스크롤 온라인 한글 패치를 작업하기 위해서 부차적으로 사용했던 함수를 저장했습니다.

## ESOKo 프로젝트 단계
1. https://esofiles.uesp.net/ 업데이트 41 en.lang 파일을 여기서 구한다
<br>
2. Esoextractdata -d 옵션으로 kr.lang 이랑 en.lang 차이를 구한다
<br>
3. Esoextractdata -l 옵션으로 kr.lang.csv 만들어서 en.lang에는 있고 kr.lang에는 없는 행을 추가한다
<br>
  3-1. Application/Translate.php 로 구글 번역한다
  <br>
4. kr.lang.csv 에서 백슬래쉬로 줄이 잘리지 않게 가공한다
https://github.com/DIPOON/ESOKRSub/blob/main/BackslashQuotationRemover.py
<br>
5. esoextractdata -x 옵션으로 kr Lang 파일 만든다
<br>
6. 완성된 kr.lang 파일을 기존 에드온 파일 gamedata 쪽에서 갈아끼운다

## 로컬 구성 가이드
라라벨 이미지 생성 <br>
`docker build -t localhost:5001/laravel:0.0.6 WebProject/`

Kind 구성 <br>
`sh Environment/KubernetesValue/local/kind-with-registry.sh`

로컬 레지스트리에 라라벨 이미지 업로드 <br>
`docker push localhost:5001/laravel:0.0.6`

이미지 확인 <br>
`curl -X GET http://localhost:5001/v2/laravel/tags/list`

DB 구성 <br>
`helm upgrade db-release oci://registry-1.docker.io/bitnamicharts/mysql -n local --create-namespace --install --kube-context kind-kind -f Environment/KubernetesValue/local/mysql-value.yaml`

WAS 구성 <br>
`helm upgrade was-release Environment/HelmChart/esoko -n local --create-namespace --install --kube-context kind-kind`

(개발 후 optional) 정리
1. `helm uninstall was-release -n local --kube-context kind-kind`
2. `kind delete cluster`

## TODO List
esokr 코드 정리
<br>
유저 참여 가능한 번역툴

## 관련 기술
<ul>
<li>Composer</li>
<li>Docker</li>
<li>HTML</li>
<li>Helm</li>
<li>Kubernetes</li>
<li>Laravel</li>
<li>PHP</li>
<li>Nginx</li>
<li>MySQL</li>
<li>Redis</li>
<li>Kind</li>
<li>CSS</li>
<li>JavaScript</li>
<li>Google Cloud Console</li>
<li>Google Kubernetes Engine</li>
<li>GoCD</li>
</ul>