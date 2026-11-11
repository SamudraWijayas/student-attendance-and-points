<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('major_id')->constrained('majors')->restrictOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
            $table->string('grade_level', 10); // 10, 11, 12
            $table->string('name', 50); // e.g. X TKJ 1, XI TKR 1
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['grade_level', 'major_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_classes');
    }
};

