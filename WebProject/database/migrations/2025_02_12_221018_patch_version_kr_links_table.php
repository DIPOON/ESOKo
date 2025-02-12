<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * kr.lang 파일 링크 걸어서
     * @return void
     */
    public function up(): void
    {
        Schema::create('patch_kr_links', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('patch'); // ESO 업데이트 패치 버전 EnumPatch 참조
            $table->text('link');          // 다운로드받을 수 있는 주소
            $table->text('note');          // 특징이나 안내
            $table->timestamps();
            $table->index(['patch']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('patch_kr_links');
    }
};
