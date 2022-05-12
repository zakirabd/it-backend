<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TeacherBonus extends Model
{
    protected $table = 'teacher_bonuses';





    protected $fillable = [


        'teacher_id',


        'bonus',
        
        'title',

        'date'
    ];
}
