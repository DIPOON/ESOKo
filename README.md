# ESOKo

엘더스크롤 온라인 한글 패치를 작업하기 위한 저장소입니다.
<br> 업데이트로 새로 추가된 문구를 추가하기에서 시작해 기계 번역을 시도한 스크립트가 있었습니다.
<br> 현재는 유저 참여 가능한 웹 번역툴을 돌리는 코드입니다.

## esokr 에드온
https://www.esoui.com/downloads/info2334-EsoKR.html

## ESOkr lang 파일 만드는 방법

1. 먼저 https://esofiles.uesp.net/ 사이트에서 en.lang 파일을 구한다
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

## 로컬 구성 가이드
필요 <br>
리눅스(WSL) 도커(도커데스크탑) Kind Helm Git Php K9s Composer

라라벨 이미지 생성 <br>
`docker build -t localhost:5001/laravel:latest WebProject/`

Kind 구성 <br>
Environment/KubernetesValue/local/kind-with-registry.sh 경로의 hostPath 여기를 자신의 경로로 수정합니다. <br>
`sh Environment/KubernetesValue/local/kind-with-registry.sh`

로컬 레지스트리에 라라벨 이미지 업로드 <br>
`docker push localhost:5001/laravel:latest`

이미지 확인 <br>
`curl -X GET http://localhost:5001/v2/laravel/tags/list`

DB 구성 <br>
`helm upgrade db-release oci://registry-1.docker.io/bitnamicharts/mysql -n local --create-namespace --install --kube-context kind-kind -f Environment/KubernetesValue/local/mysql-value.yaml`

WAS 구성 <br>
`helm upgrade was-release Environment/HelmChart/esoko -n local --create-namespace --install --kube-context kind-kind --set localMount=true`

(개발 후 optional) 정리
1. `helm uninstall was-release -n local --kube-context kind-kind`
2. `kind delete cluster`

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
</ul>

## 알려진 문제

kind-with-registry.sh invalid 명령어라고 뜨면서 실패하는 경우
line seperator 를 수정하거나 /bin/bash -> /bin/sh 수정

라라벨 /storage/ ... /.log permission denied 발생하는 경우
chmod 777 storage/ -R
