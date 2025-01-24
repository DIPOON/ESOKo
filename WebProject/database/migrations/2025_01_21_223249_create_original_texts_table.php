<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOriginalTextsTable extends Migration
{
    /**
     * Run the migrations.
     * lang_id-unknown-index-offset 마다 하나씩 있는 테이블
     * @return void
     */
    public function up()
    {
        Schema::create('original_texts', function (Blueprint $table) {
            $table->id();
            $table->integer('lang_id');            // lang 파일 ID 열
            $table->tinyInteger('unknown');        // lang 파일 Unknown 열
            $table->mediumInteger('index');        // lang 파일 Index 열
            $table->integer('offset');             // lang 파일 Offset 열
            $table->text('lang_text');             // lang 파일 Text 열
            $table->tinyInteger('version');        // 이 레코드를 넣게된 버전
            $table->tinyInteger('state');          // 번역 상태
            $table->text('trans_text');            // 번역 문장
            $table->bigInteger('user_id');         // 마지막 번역자
            $table->timestamps();
            $table->unique(['lang_id', 'unknown', 'index', 'offset', 'version']);
            $table->index(['state']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('original_texts');
    }
}
