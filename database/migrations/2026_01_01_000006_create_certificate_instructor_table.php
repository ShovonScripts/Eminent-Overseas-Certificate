<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificate_instructor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certificate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('instructor_id')->constrained()->restrictOnDelete();
            $table->unsignedTinyInteger('position')->default(1);

            $table->unique(['certificate_id', 'instructor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_instructor');
    }
};
