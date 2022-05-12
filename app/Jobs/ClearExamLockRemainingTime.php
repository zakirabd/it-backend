<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ClearExamLockRemainingTime implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $examlocked;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($examlocked)
    {
        //
        $this->examlocked = $examlocked;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->examlocked->update([
            'timer_start' => null,
        ]);
    }
}
