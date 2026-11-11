<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_settings', function (Blueprint $table) {
            $table->id();
            $table->time('check_in_start')->default('06:00:00');
            $table->time('on_time_limit')->default('07:00:00'); // Batas tepat waktu
            $table->time('late_start')->default('07:00:01');
            $table->time('check_in_end')->default('08:00:00');
            $table->integer('tolerance_minutes')->default(0);
            $table->json('active_days'); // e.g. ["Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"]
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_settings');
    }
};

