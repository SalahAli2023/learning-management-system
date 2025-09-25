<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Lesson;

class LessonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = Course::all();
        
        foreach ($courses as $course) {
            Lesson::factory()->count(5)->create([
                'course_id' => $course->id
            ]);
        }
        
         // Create regular lessons
        Lesson::factory()->count(50)->create();
        
        // Create free lessons
        Lesson::factory()->count(10)->free()->create();
        
        // Create short lessons
        Lesson::factory()->count(5)->short()->create();
        
        // Create long lessons
        Lesson::factory()->count(5)->long()->create();
        
        // Create lessons without files
        Lesson::factory()->count(3)->withoutFile()->create();
    }
}
