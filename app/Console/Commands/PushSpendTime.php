<?php

namespace App\Console\Commands;

use App\StudentExam;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PushSpendTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'push-spend-time';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert spend time in exam where exam is already existing and submited by students';

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
        $all_exam = StudentExam::where('is_submit',1)->get();
        foreach ($all_exam as $exam_item){
            if($exam_item->score >69){
                StudentExam::where('id',$exam_item->id)->update([
                    'spend_time'=>$exam_item->spend_time,
                    'updated_at'=>$exam_item->created_at
                ]);
            }
            echo "{$exam_item->id} = done\n ";
        }
    }
}
