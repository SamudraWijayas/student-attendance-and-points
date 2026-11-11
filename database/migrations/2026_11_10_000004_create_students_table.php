<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();

            // Wajib
            $table->foreignId('school_class_id')
                ->constrained('school_classes')
                ->restrictOnDelete();

            $table->string('nis', 30)->unique();
            $table->string('name', 150);

            // Opsional
            $table->string('nisn', 30)->nullable()->unique();
            $table->enum('gender', ['L', 'P'])->nullable();
            $table->string('birth_place', 100)->nullable();
            $table->date('birth_date')->nullable();
            $table->text('address')->nullable();
            $table->string('phone', 25)->nullable();
            $table->string('photo', 255)->nullable();
            $table->year('enrollment_year')->nullable();

            $table->string('status', 20)
                ->default('active')
                ->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['school_class_id', 'status']);
            $table->index(['name', 'nis']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};