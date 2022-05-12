<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StudentsLog extends Model
{
     protected $table = 'students_log';

     protected $fillable = [
         'id',
         'student_id',
         'type',
         'enroll_type',
         'course_id',
         'teacher_id',
         'lesson_mode',
         'study_mode',
         'created_at'
     ];

    protected $appends = ['teacher_name', 'course_title'];


    public function getTeacherNameAttribute(){
       
        if($this->teacher_id != null){
             $sql = DB::table('users')->where('id', $this->teacher_id)->select('first_name', 'last_name')->get();
             return $sql[0]->first_name. " ". $sql[0]->last_name;
            
        }else {
            return null;
        }
    }

    public function getCourseTitleAttribute(){
        if($this->course_id != null){
            $sql = DB::table('courses')->where('id', $this->course_id)->select('title')->get();
            return $sql[0]->title;
        }else {
            return null;
        }
    }
}
