<?php

namespace App\Console\Commands;

use App\Exam;
use App\Question;
use App\StudentExam;
use Illuminate\Console\Command;

class PushInMaxScore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'push-score';

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
        $student_exams = StudentExam::all();
        foreach ($student_exams as $exam) {
            if ($exam->exam()->exists()) {
                echo "{$exam->exam->id}\n";
                //$sumofquestionscore=Question::where('exam_id',$exam->exam->id)->sum('score');
                $sumofquestionscore = Question::where('exam_id', 20)->sum('score');
                echo "sumofquestionscore {$sumofquestionscore}\n";
                echo "\n setting student exam id {$exam->id} mark some {$exam->exam_id} \n";
                $dd = StudentExam::find($exam->id);
                $dd->max_score = $sumofquestionscore;
                $dd->save();
            }
        }
    }
}
