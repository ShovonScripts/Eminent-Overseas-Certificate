<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificate_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certificate_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('class_hours')->default(0);
            $table->unsignedSmallInteger('attendance_hours')->default(0);
            $table->timestamps();

            $table->unique(['certificate_id', 'year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_attendances');
    }
};
