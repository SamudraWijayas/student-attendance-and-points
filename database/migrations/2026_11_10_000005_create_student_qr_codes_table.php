<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_qr_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('token', 100)->unique(); // Random secure token
            $table->string('qr_image_path', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->dateTime('generated_at');
            $table->dateTime('deactivated_at')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_qr_codes');
    }
};

