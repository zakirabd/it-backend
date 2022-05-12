<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TeacherEnrollLocks extends Model
{
    protected $table = 'teacher_enroll_locks';
    protected $fillable = [
        'enroll_id',
        'student_group_id',
        'date',
        'teacher_id',
    ];
}
