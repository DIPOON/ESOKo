# GKE Standard 이전 + 인프라/DB 마이그레이션 계획

## Context
GKE Autopilot → Standard 이전 시, 인프라를 `infra` namespace로 분리하고 DB 마이그레이션을 `php artisan migrate`로 깨끗하게 동작하도록 정비한다.

---

## Step 1: DB 마이그레이션 정비 (코드 변경)

raw SQL(`DB::statement`)을 Laravel Schema Builder로 전환. 3개 파일 수정.

### 1-1. `database/migrations/2014_10_12_000000_create_users_table.php`
- **현재:** `DB::statement("ALTER TABLE users AUTO_INCREMENT = 100")`
- **결론: 현행 유지** (DB명 하드코딩 없음, Laravel 8에서 Schema Builder 대체 불가)
- Laravel 10+에서는 `$table->id()->startingValue(100)` 가능

### 1-2. `database/migrations/2024_07_26_193812_create_translation_logs_table.php`
- **제거:** `DB::statement("ALTER TABLE laravel.translation_logs CHANGE unknown unknown SMALLINT NOT NULL")`
- **대체:** 처음부터 `smallInteger`로 정의

```php
// Before
$table->tinyInteger('unknown');
// + DB::statement ALTER

// After
$table->smallInteger('unknown');
// DB::statement 삭제
```

### 1-3. `database/migrations/2025_01_21_223249_create_lang_id_unknown_index_offset_table.php`
- **제거:** 3개의 `DB::statement` 전부
- **대체:** Schema::create 내에서 올바른 스키마를 처음부터 정의

```php
Schema::create('lang_id_unknown_index_offsets', function (Blueprint $table) {
    $table->id();
    $table->integer('lang_id');
    $table->smallInteger('unknown');
    $table->mediumInteger('index');
    $table->integer('offset');
    $table->text('en_text');                    // 처음부터 포함
    $table->text('text');
    $table->tinyInteger('state');
    $table->smallInteger('claude_score')->default(-1);  // 처음부터 포함
    $table->bigInteger('user_id');
    $table->timestamps();
    $table->unique(['lang_id', 'unknown', 'index'], 'lang_id_unknown_index');  // 3컬럼 유니크
    $table->index(['state']);
    $table->index(['user_id']);
    $table->index(['claude_score']);
});
```

---

## Step 2: Elasticsearch 호스트 환경변수화 (코드 변경)

### 2-1. `WebProject/app/Common/ElasticManager.php`
현재 ES 호스트가 하드코딩되어 있어 namespace 분리 시 동작하지 않음.

```php
// Before (line 35)
->setHosts(['https://quickstart-es-http:9200'])

// After
$elasticHost = getenv('ELASTIC_SEARCH_HOST') ?: 'https://quickstart-es-http:9200';
->setHosts([$elasticHost])
```

### 2-2. `.env.example`에 추가
```
ELASTIC_SEARCH_HOST=https://quickstart-es-http:9200
```

---

## Step 3: Helm Chart 개선 (인프라 변경)

### 3-1. `values.yaml` 파라미터화
```yaml
localMount: false

image:
  repository: localhost:5001/laravel
  tag: latest
  pullPolicy: Always

elasticsearch:
  host: https://quickstart-es-http.infra.svc.cluster.local:9200
  secretName: quickstart-es-elastic-user
  certSecretName: quickstart-es-http-certs-internal

resources:
  requests:
    cpu: 100m
    memory: 256Mi
  limits:
    cpu: 500m
    memory: 512Mi
```

### 3-2. `deployment-nginx-laravel.yaml` 수정
- 하드코딩된 이미지 → `{{ .Values.image.repository }}:{{ .Values.image.tag }}`
- 하드코딩된 시크릿명 → `{{ .Values.elasticsearch.secretName }}`
- `ELASTIC_SEARCH_HOST` 환경변수 추가
- 리소스 requests/limits 추가
- Liveness/Readiness probe 추가:
  ```yaml
  livenessProbe:
    httpGet:
      path: /
      port: 80
    initialDelaySeconds: 30
    periodSeconds: 10
  readinessProbe:
    httpGet:
      path: /
      port: 80
    initialDelaySeconds: 5
    periodSeconds: 5
  ```

---

## Step 4: Namespace 분리 (인프라 변경)

### 4-1. `infra` namespace에 배치할 것
- MySQL (Bitnami Helm chart) — `helm install -n infra`
- Elasticsearch (ECK) — `엘라스틱서치.yaml`에 `namespace: infra` 추가
- Kibana — `키바나.yaml`에 `namespace: infra` 추가

### 4-2. `live` namespace에 유지할 것
- WAS Deployment (Helm chart)
- Service (service-was)
- Ingress, Certificate, FrontendConfig

### 4-3. 크로스 네임스페이스 참조 변경
| 항목 | Before | After |
|------|--------|-------|
| MySQL Host | `db-release-mysql` | `db-release-mysql.infra.svc.cluster.local` |
| ES Host | `quickstart-es-http:9200` | `quickstart-es-http.infra.svc.cluster.local:9200` |
| ES Secret | 같은 namespace | **infra에서 live로 시크릿 복제 필요** (K8s는 크로스 네임스페이스 시크릿 참조 불가) |

### 4-4. ES 시크릿 복제
K8s Secret은 namespace 간 공유가 안 되므로, infra namespace의 ES 시크릿을 live namespace로 복제해야 함:
```bash
# ES CA 인증서 시크릿 복제
kubectl get secret quickstart-es-http-certs-internal -n infra -o json \
  | jq '.metadata.namespace = "live" | del(.metadata.resourceVersion, .metadata.uid, .metadata.creationTimestamp)' \
  | kubectl apply -f -

# ES 비밀번호 시크릿 복제
kubectl get secret quickstart-es-elastic-user -n infra -o json \
  | jq '.metadata.namespace = "live" | del(.metadata.resourceVersion, .metadata.uid, .metadata.creationTimestamp)' \
  | kubectl apply -f -
```
→ ES 비밀번호 변경 시 재복제 필요. 자동화하려면 Replicator나 External Secrets 도입 필요하지만 프로젝트 규모상 수동으로 충분.

---

## Step 5: Dockerfile 멀티스테이지 빌드 (코드 변경)

prod에서 xdebug 제거.

```dockerfile
# === Base ===
FROM php:8.2.0-fpm AS base
RUN docker-php-ext-install mysqli pdo pdo_mysql
WORKDIR /var/www/html
COPY . /var/www/html

# === Dev ===
FROM base AS dev
EXPOSE 8000
RUN pecl install xdebug && docker-php-ext-enable xdebug
RUN echo "zend_extension=xdebug.so" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.log=/var/log/xdebug.log" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# === Prod (기본) ===
FROM base AS prod
```

빌드 시: `docker build --target prod` 또는 `docker build --target dev`

---

## 수정 대상 파일 요약

| 파일 | 변경 내용 |
|------|-----------|
| `WebProject/database/migrations/2024_07_26_193812_create_translation_logs_table.php` | tinyInteger→smallInteger, DB::statement 제거 |
| `WebProject/database/migrations/2025_01_21_223249_create_lang_id_unknown_index_offset_table.php` | 3개 DB::statement 제거, 스키마 재정의 |
| `WebProject/app/Common/ElasticManager.php` | ES 호스트 환경변수화 |
| `WebProject/.env.example` | ELASTIC_SEARCH_HOST 추가 |
| `WebProject/Dockerfile` | 멀티스테이지 빌드 (dev/prod 분리) |
| `Environment/HelmChart/esoko/values.yaml` | 이미지/ES/리소스 파라미터 추가 |
| `Environment/HelmChart/esoko/templates/deployment-nginx-laravel.yaml` | 파라미터 참조, probe, env 추가 |
| `Environment/KubernetesValue/live/엘라스틱서치.yaml` | namespace: infra 추가 |
| `Environment/KubernetesValue/live/키바나.yaml` | namespace: infra 추가 |

---

## 검증 방법

1. **DB 마이그레이션**: `php artisan migrate:fresh` — 빈 DB에서 에러 없이 모든 테이블 생성되는지 확인
2. **ES 연결**: `ELASTIC_SEARCH_HOST` 환경변수 세팅 후 검색 페이지 동작 확인
3. **Dockerfile**: `docker build --target prod`로 빌드 후 xdebug 미포함 확인
4. **Helm**: `helm template esoko ./HelmChart/esoko -f values.yaml`로 렌더링 결과 확인
5. **Namespace**: `kubectl get all -n infra` / `kubectl get all -n live`로 리소스 분리 확인
