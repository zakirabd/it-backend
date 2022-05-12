<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

class TeacherSchedule extends Model
{
     protected $table = 'teacher_schedule';
     
     protected $fillable = [
         'teacher_id',
         'group_id',
         'student_id',
         'study_mode',
         'time',
         'weekday',
         'start_time',
         'finish_time'
     ];
}
