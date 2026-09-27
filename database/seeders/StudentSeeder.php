<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Student;

class StudentSeeder extends Seeder
{
    
    public function run(): void
    {
        
        Student::updateOrCreate(
            ['student_number' => 'C25-01-2647-MAN121'],
            [
                'first_name' => 'Juan',
                'last_name' => 'Dela Cruz',
                'academic_level' => 'College',
                'year_level' => '2nd year',
                'course_or_strand' => 'BSCS',
            ]
        );

        // Generate 20 random dummy students for testing
        Student::factory()->count(20)->create();
    }
}
