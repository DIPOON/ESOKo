<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTermLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('term_logs', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('state');          // 번역 상태. TermLog 참조
            $table->unsignedBigInteger('term_id'); // terms id
            $table->string('term');                // 원문 용어
            $table->string('target_text');         // 번역 용어
            $table->text('note');                  // 첨언
            $table->unsignedBigInteger('user_id'); // 마지막 수정자
            $table->timestamps();
            $table->index(['term_id']);
            $table->index(['user_id']);
            $table->index(['state']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('term_logs');
    }
}
