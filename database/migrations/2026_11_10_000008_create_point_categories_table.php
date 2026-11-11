<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('point_categories', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20); // prestasi, pelanggaran
            $table->string('severity', 20)->nullable(); // ringan, sedang, berat, fatal, or null for prestasi
            $table->string('category_name', 100);
            $table->string('action_name', 200);
            $table->integer('min_points');
            $table->integer('max_points');
            $table->integer('default_points');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['type', 'severity', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_categories');
    }
};

