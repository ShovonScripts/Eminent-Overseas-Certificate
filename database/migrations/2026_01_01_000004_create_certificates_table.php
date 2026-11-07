<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('certificate_no')->nullable()->unique();
            $table->char('verification_code', 26)->nullable()->unique();
            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            $table->foreignId('course_id')->constrained()->restrictOnDelete();
            $table->date('study_start');
            $table->date('study_end');
            $table->unsignedTinyInteger('hours_per_day');
            $table->unsignedTinyInteger('days_per_week');
            $table->json('class_days');
            $table->string('national_holidays')->nullable();
            $table->unsignedSmallInteger('total_class_hours')->default(0);
            $table->unsignedSmallInteger('total_attendance_hours')->default(0);
            $table->decimal('attendance_rate', 5, 2)->default(0);
            $table->date('issued_on');
            $table->string('status', 10)->default('draft')->index();
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->string('revoke_reason')->nullable();
            $table->string('pdf_path')->nullable();
            $table->char('pdf_sha256', 64)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('student_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
