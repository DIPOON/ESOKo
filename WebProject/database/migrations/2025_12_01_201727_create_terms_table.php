<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTermsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('terms', function (Blueprint $table) {
            $table->id();
            $table->string('term');                // 원문 용어
            $table->string('target_text');         // 번역 용어
            $table->text('note');                  // 첨언
            $table->unsignedBigInteger('user_id'); // 마지막 수정자
            $table->timestamps();
            $table->unique('term');
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('terms');
    }
}
