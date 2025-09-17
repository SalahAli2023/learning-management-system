<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\User;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $instructors = User::where('role','instructor')->get();
        
        foreach ($instructors as $instructor) {
            Course::factory()->count(2)->create([
                'instructor_id' => $instructor->id
            ]);
        }
    }
}
