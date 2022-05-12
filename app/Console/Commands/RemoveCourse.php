<?php

namespace App\Console\Commands;

use App\Course;
use App\CourseAssign;
use Illuminate\Console\Command;

class RemoveCourse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove-course';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'remove course_id from course_assign table,where course_id not exist in course table';

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
        $all_courses = Course::all()->pluck('id');

        $course_assign = CourseAssign::whereNotIn('course_id',$all_courses)->get();
        foreach ($course_assign as $item_course_assign){
            echo "course_assign =  $item_course_assign id= $item_course_assign->id\n";
            CourseAssign::where('id',$item_course_assign->id)->delete();
            echo "done\n";
        }
//        echo "course_assign =  $course_assign\n";
    }
}
