<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name_ja');
            $table->string('name_en');
            $table->string('level_code', 20);
            $table->string('aim_level_ja');
            $table->string('aim_level_en');
            $table->unsignedSmallInteger('total_hours');
            $table->string('book_ja');
            $table->string('book_en');
            $table->json('learning_context');
            $table->unsignedTinyInteger('default_hours_per_day')->default(3);
            $table->unsignedTinyInteger('default_days_per_week')->default(3);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
