<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('student_number')->unique(); // USN
            $table->string('first_name');
            $table->string('last_name');
            $table->string('academic_level')->nullable(); // e.g. College / SHS
            $table->string('year_level')->nullable();     // e.g. 1st Year / Grade 11
            $table->string('course_or_strand')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};