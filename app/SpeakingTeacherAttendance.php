<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SpeakingTeacherAttendance extends Model
{
    protected $table = 'speaking_teacher_attendance';
     
    protected $fillable = [
        'id',
        'company_id',
        'teacher_id',
        'student_id'
    ];
}
