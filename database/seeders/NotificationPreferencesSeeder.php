<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotificationPreferencesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        
        $defaultPreferences = [
            'new_assignments' => true,
            'assignment_graded' => true,
            'enrollment_updates' => true,
            'deadline_reminders' => true,
            'course_updates' => true,
            'system_announcements' => true,
        ];
        
        foreach ($users as $user) {
            $user->update([
                'notification_preferences' => $defaultPreferences,
                'email_notifications' => true,
                'push_notifications' => true,
            ]);
        }
    }
}
