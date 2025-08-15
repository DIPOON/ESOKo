# ESOKo

엘더스크롤 온라인 한글 패치를 작업하기 위한 저장소입니다.
<br> 업데이트로 새로 추가된 문구를 추가하기에서 시작해 기계 번역을 시도한 스크립트가 있었습니다.
<br> 현재는 유저 참여 가능한 웹 번역 사이트를 돌리는 코드입니다.

## esokr 사이트
http://www.esokr.org/

## esokr 에드온
https://www.esoui.com/downloads/info2334-EsoKR.html

## 최신 lang 파일 업데이트 시 관리 방법
1. https://esofiles.uesp.net/ 사이트에서 en.lang 파일을 구한다.
1. EsoExtractData -d (old en.lang 파일명) (new en.lang 파일명) 옵션으로 added, changed, removed 파일을 얻는다.
1. 기존 lang_id_unknown_index_offsets 테이블 백업한다.
1. InsertEnLangAdded.php 스크립트로 TODO 확인하고 새롭게 추가된 added 파일 값을 추가한다.
1. UpdateEnLangChanged.php 스크립트로 수정된 changed 파일 값을 이용한다. 
1. RemoveLang.php 스크립트로 삭제된 removed 파일을 사용한다.
1. CreateKrLang.php 스크립트로 kr.lang.csv 을 만든다.
1. esoextractdata -x 옵션으로 kr.lang 파일 만든다
1. 새롭게 만든 kr.lang 과 새로운 en.lang 데이터 숫자가 맞는지 확인한다.
1. 구글 버킷에 kr.lang 파일을 올린다. SHA-1 이용해서 분류합니다.
1. patch_kr_links 테이블에 추가한다.
1. 다운로드 페이지에서 잘 받아지는지 확인한다.

## 로컬 구성 가이드
필요 <br>
리눅스(WSL) 도커(도커데스크탑) Kind Helm Git Php K9s Composer

라라벨 이미지 생성 <br>
```bash
docker build -t localhost:5001/laravel:latest WebProject/ 
```

Kind 구성 <br>
Environment/KubernetesValue/local/kind-with-registry.sh 경로의 hostPath 여기를 자신의 경로로 수정합니다. <br>
```bash
sh Environment/KubernetesValue/local/kind-with-registry.sh
```

로컬 레지스트리에 라라벨 이미지 업로드 <br>
```bash
docker push localhost:5001/laravel:latest
```

이미지 확인 <br>
```bash
curl -X GET http://localhost:5001/v2/laravel/tags/list
```

MySQL DB 구성 <br>
```bash
helm upgrade db-release oci://registry-1.docker.io/bitnamicharts/mysql -n local --create-namespace --install --kube-context kind-kind -f Environment/KubernetesValue/local/mysql-value.yaml
```

Elastic stack 구성 <br>
```bash
helm repo add elastic https://helm.elastic.co
```
```bash
helm upgrade elastic-operator elastic/eck-operator -n local --create-namespace --install --kube-context kind-kind
```
```bash
cat <<EOF | kubectl apply --namespace=local -f -
apiVersion: elasticsearch.k8s.elastic.co/v1
kind: Elasticsearch
metadata:
  name: quickstart
spec:
  version: 8.17.3
  nodeSets:
  - name: default
    count: 1
    config:
      node.store.allow_mmap: false
EOF
```
```bash
cat <<EOF | kubectl apply --namespace=local -f -
apiVersion: kibana.k8s.elastic.co/v1
kind: Kibana
metadata:
  name: quickstart
spec:
  version: 8.17.3
  count: 1
  elasticsearchRef:
    name: quickstart
EOF
```

WAS 구성 <br>
```bash
helm upgrade was-release Environment/HelmChart/esoko -n local --create-namespace --install --kube-context kind-kind --set localMount=true
```

(개발 후 optional) 정리
1. WAS 정리
```bash
helm uninstall was-release -n local --kube-context kind-kind
```
2. Kind 정리
```bash
kind delete cluster
```

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
<li>WSL</li>
</ul>

## 알려진 문제

kind-with-registry.sh invalid 명령어라고 뜨면서 실패하는 경우
line seperator 를 수정하거나 /bin/bash -> /bin/sh 수정
