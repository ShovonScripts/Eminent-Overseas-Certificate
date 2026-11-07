<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->dropIndex(['student_id']);
            $table->dropColumn('student_id');

            $table->string('student_name_en')->after('course_id');
            $table->string('student_name_ja')->nullable()->after('student_name_en');
            $table->date('student_date_of_birth')->after('student_name_ja');
            $table->string('student_gender', 20)->after('student_date_of_birth');
            $table->string('student_email')->nullable()->after('student_gender');
            $table->string('student_phone', 30)->nullable()->after('student_email');
        });
    }

    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropColumn([
                'student_name_en',
                'student_name_ja',
                'student_date_of_birth',
                'student_gender',
                'student_email',
                'student_phone',
            ]);
            $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete();
        });
    }
};
