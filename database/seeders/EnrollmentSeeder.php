<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;

class EnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // طلاب
        $students = User::factory()->count(5)->create(['role' => 'student']);

        // مدرب + كورسات
        $instructor = User::factory()->create(['role' => 'instructor']);
        $courses = Course::factory()->count(3)->create(['instructor_id' => $instructor->id]);

        // تسجيل عشوائي
        foreach ($students as $student) {
            foreach ($courses->random(2) as $course) {
                Enrollment::create([
                    'student_id' => $student->id,
                    'course_id'  => $course->id,
                    'status'     => fake()->randomElement(['pending', 'active', 'completed']),
                ]);
            }
        }
    }
}
