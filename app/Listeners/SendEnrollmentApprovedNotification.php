<?php

namespace App\Listeners;

use App\Events\EnrollmentApproved;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendEnrollmentApprovedNotification 
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(EnrollmentApproved $event): void
    {
        //
    }
}
