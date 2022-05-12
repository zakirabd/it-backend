<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TeacherLock extends Model
{

    protected $fillable = [
        'teacher_id',
        'date',
        'lock_status',
    ];
}
