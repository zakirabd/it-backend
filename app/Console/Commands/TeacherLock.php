<?php



namespace App\Console\Commands;



use App\Mail\TestMailSend;

use App\User;

use Carbon\Carbon;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\Mail;



class TeacherLock extends Command

{

    /**

     * The name and signature of the console command.

     *

     * @var string

     */

    protected $signature = 'teacher:lock';



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

    {/////////

        $getToday = Carbon::today()->format('Y-m-d');

        //get all teacher for locked first time.

        $users = User::whereIn('role', ['head_teacher', 'teacher'])->get();




        foreach ($users as $teachers) {

            $check_date = Carbon::parse($teachers->created_at->addWeeks(12))->format('Y-m-d');

            if ($check_date <= $getToday) {

                echo "first date  $check_date \n";

                echo "today  $getToday \n";

                $count_lock_table = \App\TeacherLock::where('teacher_id', $teachers->id)->count();

                if ($count_lock_table == 1) {

                    $get_existing_teacher =\App\TeacherLock::where('teacher_id', $teachers->id )->first();

                    $date_check_secend_time = Carbon::parse($get_existing_teacher->date)->addWeeks(12);

                    echo "date_check_secend_time  $date_check_secend_time \n";

                    echo "today  $getToday \n";

                    //checking 12 weeks by latest date

                    if ($date_check_secend_time <= $getToday) {

                    \App\TeacherLock::where('teacher_id', $get_existing_teacher->teacher_id)->update(

                        [

                            'teacher_id' => $get_existing_teacher->teacher_id,

                            'lock_status' => 1,

                            'date' => $getToday,



                        ]

                    );

                        echo "update for final time ==== $get_existing_teacher->id  \n";

                    }else{



                        echo "not exist any data  \n";

                    }



                } else {

                    \App\TeacherLock::where('id', $teachers->id)->insert(

                        [

                            'teacher_id' => $teachers->id,

                            'lock_status' => 1,

                            'date' => $getToday,



                        ]

                    );

                    echo "add  \n";

                }

            }

        }

        \Log::info("Cron is working fine!");


/////
    }

    ////////

}

