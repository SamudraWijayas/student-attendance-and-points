<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('point_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('point_category_id')->constrained('point_categories')->restrictOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
            $table->foreignId('semester_id')->nullable()->constrained('semesters')->nullOnDelete();
            $table->string('type', 20); // prestasi, pelanggaran
            $table->string('severity', 20)->nullable(); // ringan, sedang, berat, fatal
            $table->integer('points'); // Positive for prestasi (+), negative for pelanggaran (-)
            $table->date('incident_date');
            $table->text('description');
            $table->string('evidence_path', 255)->nullable();
            $table->foreignId('recorded_by')->constrained('users')->restrictOnDelete();
            $table->boolean('is_cancelled')->default(false);
            $table->text('cancellation_reason')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('cancelled_at')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'type', 'is_cancelled']);
            $table->index(['semester_id', 'incident_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_transactions');
    }
};

