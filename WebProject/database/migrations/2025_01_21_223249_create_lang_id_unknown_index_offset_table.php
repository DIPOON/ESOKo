<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * lang_id-unknown-index-offset 마다 하나씩 있는 테이블
     * en.lang 에서 이 테이블 데이터 생성/삭제
     * 이 테이블의 값으로 kr.lang 만듬
     * @return void
     */
    public function up()
    {
        Schema::create('lang_id_unknown_index_offsets', function (Blueprint $table) {
            $table->id();
            $table->integer('lang_id');            // lang 파일 ID 열
            $table->tinyInteger('unknown');        // lang 파일 Unknown 열
            $table->mediumInteger('index');        // lang 파일 Index 열
            $table->integer('offset');             // lang 파일 Offset 열
            $table->text('text');                  // lang 파일 Text 열이나 번역된 문장
            $table->tinyInteger('state');          // 번역 상태
            $table->bigInteger('user_id');         // 마지막 번역자
            $table->timestamps();
            $table->unique(['lang_id', 'unknown', 'index', 'offset'], 'identifier');
            $table->index(['state']);
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lang_id_unknown_index_offsets');
    }
};
