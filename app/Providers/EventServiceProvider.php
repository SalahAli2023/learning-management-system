<?php

namespace App\Providers;

use App\Events\AssignmentCreated;
use App\Events\AssignmentSubmitted;
use App\Events\EnrollmentApproved;
use App\Events\AssignmentGraded;
use App\Events\CoursePublished;
use App\Listeners\SendNewAssignmentNotification;
use App\Listeners\SendAssignmentSubmittedNotification;
use App\Listeners\SendAssignmentGradedNotification;
use App\Listeners\SendEnrollmentApprovedNotification;
use App\Listeners\SendCoursePublishedNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{

    protected $listen = [
        AssignmentCreated::class => [
            SendNewAssignmentNotification::class,
        ],
        AssignmentSubmitted::class => [
            SendAssignmentSubmittedNotification::class,
        ],
        AssignmentGraded::class => [
            SendAssignmentGradedNotification::class,
        ],
        EnrollmentApproved::class => [
            SendEnrollmentApprovedNotification::class,
        ],
        CoursePublished::class => [
            SendCoursePublishedNotification::class,
        ],
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
