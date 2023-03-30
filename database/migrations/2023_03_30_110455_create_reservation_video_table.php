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
        Schema::create('reservation_video', function (Blueprint $table) {
            $table->bigIncrements('reservation_video_no');
            $table->bigInteger('reservation_no');
            $table->integer('camera_no');
            $table->text('vedio_url');
            $table->text('vedio_storage_url');
            $table->char('state',1)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservation_video');
    }
};
