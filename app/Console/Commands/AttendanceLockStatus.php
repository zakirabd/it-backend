<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;

class AttendanceLockStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:lock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $allUser = User::where('role', 'student')->get();

        foreach ($allUser as $user) {
            echo "lock status $user->lock_status \n";

            if ($user->lock_status == 1) {
                echo "user id  $user->id \n";

                User::findOrFail($user->id)->update([
                    'attendance_lock_status' => 1
                ]);
            }

        }
    }
}
