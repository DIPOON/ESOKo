<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * lang_id-unknown-index-offset 마다 하나씩 있는 테이블 - TODO 나중에 offset 상관 없는 것 알아서 테이블 명 수정 필요함
     * en.lang 에서 이 테이블 데이터 생성/삭제
     * 이 테이블의 값으로 kr.lang 만듬
     * @return void
     */
    public function up(): void
    {
        Schema::create('lang_id_unknown_index_offsets', function (Blueprint $table) {
            $table->id();
            $table->integer('lang_id');            // lang 파일 ID 열, section id
            $table->smallInteger('unknown');       // lang 파일 Unknown 열, section index
            $table->mediumInteger('index');        // lang 파일 Index 열, string index
            $table->integer('offset');             // lang 파일 Offset 열
            $table->text('text');                  // lang 파일 Text 열이나 번역된 문장
            $table->tinyInteger('state');          // 번역 상태
            $table->bigInteger('user_id');         // 마지막 번역자
            $table->timestamps();
            $table->unique(['lang_id', 'unknown', 'index', 'offset'], 'identifier');
            $table->index(['state']);
            $table->index(['user_id']);
        });

        // 'lang_id', 'unknown', 'index' 이것만으로도 유니크 걸려서 유니크 수정했음
        DB::statement("ALTER TABLE `lang_id_unknown_index_offsets` DROP INDEX `identifier`, ADD  UNIQUE INDEX `identifier` (`lang_id` ASC, `unknown` ASC, `index` ASC) VISIBLE");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('lang_id_unknown_index_offsets');
    }
};
