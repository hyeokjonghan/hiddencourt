<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reservation', function (Blueprint $table) {
            $table->bigIncrements('reservation_no');
            $table->date('reservation_date');
            $table->time('reservation_start_time');
            $table->time('reservation_end_time');
            $table->string('reservation_name', 255);
            $table->char('state',1)->comment('0: 영상 요청 대기, 1: 영상 요청 완료, 2: 영상 다운 완료')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservation');
    }
};
