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
        Schema::create('camera', function (Blueprint $table) {
            $table->bigIncrements('camera_no');
            $table->string('camera_id',50);
            $table->string('cam_name',50);
            $table->string('mac_id',50);
            $table->string('serial_number',50);
            $table->string('cam_group_id',50);
            $table->string('model_name', 50);
            $table->string('cam_firmware', 50);
            $table->string('cam_group_name', 50);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('camera');
    }
};
