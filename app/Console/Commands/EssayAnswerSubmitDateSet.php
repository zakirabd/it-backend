<?php

namespace App\Console\Commands;

use App\EssayAnswer;
use Illuminate\Console\Command;

class EssayAnswerSubmitDateSet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'essay-answer:submit-date';

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
          $data = EssayAnswer::where('is_submitted',1)->get();
          foreach($data as $Esaay_answer){
              EssayAnswer::where('id',$Esaay_answer->id)->update([
                  'submit_date'=>$Esaay_answer->created_at
              ]);
              echo "{$Esaay_answer->id} = done\n ";

          }

    }
}
