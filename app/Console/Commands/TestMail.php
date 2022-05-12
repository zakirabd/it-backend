<?php



namespace App\Console\Commands;



use App\Mail\TestMailSend;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\Mail;

use Carbon\Carbon;

use App\User;


class TestMail extends Command

{

    /**

     * The name and signature of the console command.

     *

     * @var string

     */

    protected $signature = 'test:mail';



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
        // $users = User::get();

        // if(Carbon::now()->format('Y/m/d H:i') == '2021/12/31 11:00'){
        //     $inc = 0;
        //     foreach($users as $user){
        //         if($inc < 200){
        //             Mail::to($user->email)->send(new TestMailSend($user));
        //         }
        //         $inc++;
        //     }
        // }

        // if(Carbon::now()->format('Y/m/d H:i') == '2021/12/31 11:03'){
        //     $inc = 0;
        //     foreach($users as $user){
        //         if($inc >= 200 && $inc < 400){
        //             Mail::to($user->email)->send(new TestMailSend($user));
        //         }
        //         $inc++;
        //     }
        // }

        // if(Carbon::now()->format('Y/m/d H:i') == '2021/12/31 11:06'){
        //     $inc = 0;
        //     foreach($users as $user){
        //         if($inc >= 400 && $inc < 600){
        //             Mail::to($user->email)->send(new TestMailSend($user));
        //         }
        //         $inc++;
        //     }
        // }

        // if(Carbon::now()->format('Y/m/d H:i') == '2021/12/31 11:09'){
        //     $inc = 0;
        //     foreach($users as $user){
        //         if($inc >= 600 && $inc < 800){
        //             Mail::to($user->email)->send(new TestMailSend($user));
        //         }
        //         $inc++;
        //     }
        // }

        // if(Carbon::now()->format('Y/m/d H:i') == '2021/12/31 11:12'){
        //     $inc = 0;
        //     foreach($users as $user){
        //         if($inc >= 800 && $inc < 1000){
        //             Mail::to($user->email)->send(new TestMailSend($user));
        //         }
        //         $inc++;
        //     }
        // }

        // if(Carbon::now()->format('Y/m/d H:i') == '2021/12/31 11:15'){
        //     $inc = 0;
        //     foreach($users as $user){
        //         if($inc >= 1000 && $inc < 1200){
        //             Mail::to($user->email)->send(new TestMailSend($user));
        //         }
        //         $inc++;
        //     }
        // }

        // if(Carbon::now()->format('Y/m/d H:i') == '2021/12/31 11:18'){
        //     $inc = 0;
        //     foreach($users as $user){
        //         if($inc >= 1200 && $inc < 1400){
        //             Mail::to($user->email)->send(new TestMailSend($user));
        //         }
        //         $inc++;
        //     }
        // }

        // if(Carbon::now()->format('Y/m/d H:i') == '2021/12/31 11:21'){
        //     $inc = 0;
        //     foreach($users as $user){
        //         if($inc >= 1400 && $inc < 1600){
        //             Mail::to($user->email)->send(new TestMailSend($user));
        //         }
        //         $inc++;
        //     }
        // }

        // if(Carbon::now()->format('Y/m/d H:i') == '2021/12/31 11:24'){
        //     $inc = 0;
        //     foreach($users as $user){
        //         if($inc >= 1600 && $inc < 1800){
        //             Mail::to($user->email)->send(new TestMailSend($user));
        //         }
        //         $inc++;
        //     }
        // }

        // if(Carbon::now()->format('Y/m/d H:i') == '2021/12/31 11:27'){
        //     $inc = 0;
        //     foreach($users as $user){
        //         if($inc >= 1800 && $inc < 2000){
        //             Mail::to($user->email)->send(new TestMailSend($user));
        //         }
        //         $inc++;
        //     }
        // }

        // if(Carbon::now()->format('Y/m/d H:i') == '2021/12/31 11:30'){
        //     $inc = 0;
        //     foreach($users as $user){
        //         if($inc >= 2000 && $inc < 2200){
        //             Mail::to($user->email)->send(new TestMailSend($user));
        //         }
        //         $inc++;
        //     }
        // }

        // if(Carbon::now()->format('Y/m/d H:i') == '2021/12/31 11:33'){
        //     $inc = 0;
        //     foreach($users as $user){
        //         if($inc >= 2200 && $inc < 2400){
        //             Mail::to($user->email)->send(new TestMailSend($user));
        //         }
        //         $inc++;
        //     }
        // }
        
        // if(Carbon::now()->format('Y/m/d H:i') == '2021/12/31 11:36'){
        //     $inc = 0;
        //     foreach($users as $user){
        //         if($inc >= 2400 && $inc < 2600){
        //             Mail::to($user->email)->send(new TestMailSend($user));
        //         }
        //         $inc++;
        //     }
        // }

        // if(Carbon::now()->format('Y/m/d H:i') == '2021/12/31 11:39'){
        //     $inc = 0;
        //     foreach($users as $user){
        //         if($inc >= 2600 && $inc < 2800){
        //             Mail::to($user->email)->send(new TestMailSend($user));
        //         }
        //         $inc++;
        //     }
        // }


        // if(Carbon::now()->format('Y/m/d H:i') == '2021/12/31 11:42'){
        //     $inc = 0;
        //     foreach($users as $user){
        //         if($inc >= 2800 && $inc < 3000){
        //             Mail::to($user->email)->send(new TestMailSend($user));
        //         }
        //         $inc++;
        //     }
        // }

        // if(Carbon::now()->format('Y/m/d H:i') == '2021/12/31 11:45'){
        //     $inc = 0;
        //     foreach($users as $user){
        //         if($inc >= 3000 && $inc < 3200){
        //             Mail::to($user->email)->send(new TestMailSend($user));
        //         }
        //         $inc++;
        //     }
        // }

        // if(Carbon::now()->format('Y/m/d H:i') == '2021/12/31 11:48'){
        //     $inc = 0;
        //     foreach($users as $user){
        //         if($inc >= 3200 && $inc < 3400){
        //             Mail::to($user->email)->send(new TestMailSend($user));
        //         }
        //         $inc++;
        //     }
        // }

        // if(Carbon::now()->format('Y/m/d H:i') == '2021/12/31 11:51'){
        //     $inc = 0;
        //     foreach($users as $user){
        //         if($inc >= 3400 && $inc < 3600){
        //             Mail::to($user->email)->send(new TestMailSend($user));
        //         }
        //         $inc++;
        //     }
        // }

        // if(Carbon::now()->format('Y/m/d H:i') == '2021/12/31 11:54'){
        //     $inc = 0;
        //     foreach($users as $user){
        //         if($inc >= 3600 && $inc < 3800){
        //             Mail::to($user->email)->send(new TestMailSend($user));
        //         }
        //         $inc++;
        //     }
        // }

        // if(Carbon::now()->format('Y/m/d H:i') == '2021/12/31 11:57'){
        //     $inc = 0;
        //     foreach($users as $user){
        //         if($inc >= 3800 && $inc < 4000){
        //             Mail::to($user->email)->send(new TestMailSend($user));
        //         }
        //         $inc++;
        //     }
        // }

        // if(Carbon::now()->format('Y/m/d H:i') == '2021/12/31 12:00'){
        //     $inc = 0;
        //     foreach($users as $user){
        //         if($inc >= 4000 && $inc < 4200){
        //             Mail::to($user->email)->send(new TestMailSend($user));
        //         }
        //         $inc++;
        //     }
        // }

        // if(Carbon::now()->format('Y/m/d H:i') == '2021/12/31 12:03'){
        //     $inc = 0;
        //     foreach($users as $user){
        //         if($inc >= 4200 && $inc < 4400){
        //             Mail::to($user->email)->send(new TestMailSend($user));
        //         }
        //         $inc++;
        //     }
        // }


        // if(Carbon::now()->format('Y/m/d H:i') == '2021/12/31 12:06'){
        //     $inc = 0;
        //     foreach($users as $user){
        //         if($inc >= 4400 && $inc < 4600){
        //             Mail::to($user->email)->send(new TestMailSend($user));
        //         }
        //         $inc++;
        //     }
        // }

        // if(Carbon::now()->format('Y/m/d H:i') == '2021/12/31 12:09'){
        //     $inc = 0;
        //     foreach($users as $user){
        //         if($inc >= 4600 && $inc < 4800){
        //             Mail::to($user->email)->send(new TestMailSend($user));
        //         }
        //         $inc++;
        //     }
        // }


        // if(Carbon::now()->format('Y/m/d H:i') == '2021/12/31 12:12'){
        //     $inc = 0;
        //     foreach($users as $user){
        //         if($inc >= 4800 && $inc < 5000){
        //             Mail::to($user->email)->send(new TestMailSend($user));
        //         }
        //         $inc++;
        //     }
        // }


        // if(Carbon::now()->format('Y/m/d H:i') == '2021/12/31 12:15'){
        //     $inc = 0;
        //     foreach($users as $user){
        //         if($inc >= 5000 && $inc < 5200){
        //             Mail::to($user->email)->send(new TestMailSend($user));
        //         }
        //         $inc++;
        //     }
        // }

        // if(Carbon::now()->format('Y/m/d H:i') == '2021/12/31 12:18'){
        //     $inc = 0;
        //     foreach($users as $user){
        //         if($inc >= 5200 && $inc < 5400){
        //             Mail::to($user->email)->send(new TestMailSend($user));
        //         }
        //         $inc++;
        //     }
        // }

        // if(Carbon::now()->format('Y/m/d H:i') == '2021/12/31 12:21'){
        //     $inc = 0;
        //     foreach($users as $user){
        //         if($inc >= 5400 && $inc < 5600){
        //             Mail::to($user->email)->send(new TestMailSend($user));
        //         }
        //         $inc++;
        //     }
        // }

        // if(Carbon::now()->format('Y/m/d H:i') == '2021/12/31 12:24'){
        //     $inc = 0;
        //     foreach($users as $user){
        //         if($inc >= 5600 && $inc < 5800){
        //             Mail::to($user->email)->send(new TestMailSend($user));
        //         }
        //         $inc++;
        //     }
        // }

        // if(Carbon::now()->format('Y/m/d H:i') == '2021/12/31 12:27'){
        //     $inc = 0;
        //     foreach($users as $user){
        //         if($inc >= 5800 && $inc < 6000){
        //             Mail::to($user->email)->send(new TestMailSend($user));
        //         }
        //         $inc++;
        //     }
        // }

        // if(Carbon::now()->format('Y/m/d H:i') == '2021/12/31 12:30'){
        //     $inc = 0;
        //     foreach($users as $user){
        //         if($inc >= 6000 && $inc < 6200){
        //             Mail::to($user->email)->send(new TestMailSend($user));
        //         }
        //         $inc++;
        //     }
        // }


        // if(Carbon::now()->format('Y/m/d H:i') == '2021/12/31 12:33'){
        //     $inc = 0;
        //     foreach($users as $user){
        //         if($inc >= 6200 && $inc < 6400){
        //             Mail::to($user->email)->send(new TestMailSend($user));
        //         }
        //         $inc++;
        //     }
        // }

        // if(Carbon::now()->format('Y/m/d H:i') == '2021/12/31 12:36'){
        //     $inc = 0;
        //     foreach($users as $user){
        //         if($inc >= 6400 && $inc < 6600){
        //             Mail::to($user->email)->send(new TestMailSend($user));
        //         }
        //         $inc++;
        //     }
        // }

        // if(Carbon::now()->format('Y/m/d H:i') == '2021/12/31 12:39'){
        //     $inc = 0;
        //     foreach($users as $user){
        //         if($inc >= 6600 && $inc < 6800){
        //             Mail::to($user->email)->send(new TestMailSend($user));
        //         }
        //         $inc++;
        //     }
        // }


        // if(Carbon::now()->format('Y/m/d H:i') == '2021/12/31 12:42'){
        //     $inc = 0;
        //     foreach($users as $user){
        //         if($inc >= 6800 && $inc < 7000){
        //             Mail::to($user->email)->send(new TestMailSend($user));
        //         }
        //         $inc++;
        //     }
        // }

        // if(Carbon::now()->format('Y/m/d H:i') == '2021/12/31 12:45'){
        //     $inc = 0;
        //     foreach($users as $user){
        //         if($inc >= 7000 && $inc < 7200){
        //             Mail::to($user->email)->send(new TestMailSend($user));
        //         }
        //         $inc++;
        //     }
        // }

        // if(Carbon::now()->format('Y/m/d H:i') == '2021/12/31 12:48'){
        //     $inc = 0;
        //     foreach($users as $user){
        //         if($inc >= 7200 && $inc < 7400){
        //             Mail::to($user->email)->send(new TestMailSend($user));
        //         }
        //         $inc++;
        //     }
        // }


        // if(Carbon::now()->format('Y/m/d H:i') == '2021/12/31 12:51'){
        //     $inc = 0;
        //     foreach($users as $user){
        //         if($inc >= 7400 && $inc < 7600){
        //             Mail::to($user->email)->send(new TestMailSend($user));
        //         }
        //         $inc++;
        //     }
        // }
       


//        error_log('one');

    }

}

