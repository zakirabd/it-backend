<?php

namespace App\Listeners;

use App\Events\CalendarEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CalendarListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  CalendarEvent  $event
     * @return void
     */
    public function handle(CalendarEvent $event)
    {
        //
    }
}
