<?php



namespace App\Console;



use App\Console\Commands\TeacherLock;

use Illuminate\Console\Scheduling\Schedule;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;



class Kernel extends ConsoleKernel

{

    /**

     * The Artisan commands provided by your application.

     *

     * @var array

     */

    protected $commands = [

       TeacherLock::class,

    ];



    /**

     * Define the application's command schedule.

     *

     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule

     * @return void

     */

    protected function schedule(Schedule $schedule)

    {

/// weekly()

        $schedule->command('teacher:lock')->weekly();

        // $schedule->command('active:user')->everyMinute();

         $schedule->command('test:mail')->everyMinute();
// test:mail
    }



    /**

     * Register the commands for the application.

     *

     * @return void

     */

    protected function commands()

    {

        $this->load(__DIR__.'/Commands');



        require base_path('routes/console.php');

    }

}

