<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTranslationLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('translation_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('lang_id');            // lang 파일 ID 열
            $table->tinyInteger('unknown');        // lang 파일 Unknown 열
            $table->mediumInteger('index');        // lang 파일 Index 열
            $table->integer('offset');             // lang 파일 Offset 열
            $table->text('text');                  // lang 파일 Text 열
            $table->smallInteger('version');       // 이 레코드를 넣게된 버전
            $table->tinyInteger('state');          // 번역 상태
            $table->tinyInteger('user_id');        // 번역자
            $table->timestamps();
            $table->index(['lang_id', 'unknown', 'index', 'offset', 'version']);
            $table->index(['user_id']);
        });

        // unknown 128 넘는 경우가 있어서 바꿈 ;
        DB::statement("ALTER TABLE `laravel`.`translation_logs` CHANGE `unknown` `unknown` SMALLINT NOT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('translation_logs');
    }
}
