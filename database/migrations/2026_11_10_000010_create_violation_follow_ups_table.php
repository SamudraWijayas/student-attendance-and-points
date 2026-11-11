<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('violation_follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('point_transaction_id')->constrained('point_transactions')->cascadeOnDelete();
            $table->string('follow_up_type', 100); // Teguran Lisan, Konseling, Surat Pernyataan, SP 1, SP 2, SP 3, Panggilan Ortu, Skorsing, Sidang Pleno
            $table->date('follow_up_date');
            $table->string('status', 20)->default('belum_ditangani'); // belum_ditangani, dalam_proses, selesai
            $table->text('counseling_notes')->nullable();
            $table->string('statement_letter_path', 255)->nullable();
            $table->string('warning_letter_type', 20)->nullable(); // sp1, sp2, sp3
            $table->string('warning_letter_path', 255)->nullable();
            $table->date('parent_invitation_date')->nullable();
            $table->string('parent_invitation_status', 20)->nullable(); // dikirim, hadir, tidak_hadir
            $table->text('resolution_notes')->nullable();
            $table->foreignId('handled_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->index(['student_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('violation_follow_ups');
    }
};

