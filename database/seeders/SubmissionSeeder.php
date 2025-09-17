<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Assignment;
use App\Models\User;
use App\Models\Submission;

class SubmissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = User::where('role','student')->get();
        
        Assignment::all()->each(function($assignment) use ($students) {
            foreach ($students->random(3) as $student) {
                Submission::factory()->create([
                    'assignment_id' => $assignment->id,
                    'student_id' => $student->id
                ]);
            }
        });
    }
}
