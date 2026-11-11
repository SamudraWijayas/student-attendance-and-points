<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('semester_point_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained('semesters')->cascadeOnDelete();
            $table->integer('total_achievement_points')->default(0);
            $table->integer('total_minor_violations')->default(0);
            $table->integer('total_moderate_violations')->default(0);
            $table->integer('total_heavy_violations')->default(0);
            $table->integer('total_fatal_violations')->default(0);
            $table->string('active_sp_level', 20)->nullable();
            $table->dateTime('snapshot_date');
            $table->foreignId('processed_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->unique(['student_id', 'semester_id'], 'uniq_student_semester_snapshot');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('semester_point_snapshots');
    }
};

