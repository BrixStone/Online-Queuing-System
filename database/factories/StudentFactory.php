<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_number' => 'C' . fake()->numberBetween(10, 99) . '-01-' . fake()->numberBetween(1000, 9999) . '-MAN' . fake()->numberBetween(100, 999),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'academic_level' => fake()->randomElement(['College', 'Senior High School']),
            'year_level' => fake()->randomElement(['1st year', '2nd year', '3rd year', '4th year', 'Grade 11', 'Grade 12']),
            'course_or_strand' => fake()->randomElement(['BSCS', 'BSIT', 'BSBA', 'STEM', 'ABM', 'HUMSS', 'ICT']),
        ];
    }
}
