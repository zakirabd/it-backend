<?php

namespace App\Console\Commands;
use App\Events\OnlineUserEvent;
use Illuminate\Console\Command;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\DB;
use Cache;


class ActiveUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'active:user';

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

        $users = DB::table('users')->where('role','student')->get();
        $users_active = array();
        foreach ($users as $user) {
            if (Cache::has('user-is-online' . $user->id)) {
                array_push($users_active, $user);
            }
        }
        $event = count($users_active);
        event(new OnlineUserEvent($event));
        \Log::info("Active user Cron is working fine!");

    }
}
