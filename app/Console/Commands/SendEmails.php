<?php

namespace App\Console\Commands;

use App\Notifications\BirthdayWish;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:birthday';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends greetings on users birthday.';

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
        $users = User::whereDate('date_of_birth', today())->get();

        Notification::send($users, new BirthdayWish());
    }
}
