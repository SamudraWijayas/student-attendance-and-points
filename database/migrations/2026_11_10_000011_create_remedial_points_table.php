<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('remedial_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('point_transaction_id')->constrained('point_transactions')->cascadeOnDelete();
            $table->string('activity_type', 150); // Kerja Bakti Sosial, Tugas Amal Sholeh
            $table->date('activity_date');
            $table->integer('points_deducted'); // Positive integer representing the reduction of minus
            $table->text('description');
            $table->text('verification_notes')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->index(['student_id', 'activity_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('remedial_points');
    }
};

