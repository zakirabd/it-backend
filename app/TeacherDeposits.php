<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TeacherDeposits extends Model
{
    protected $table = 'teacher_deposits';





    protected $fillable = [


        'teacher_id',


        'bonus',
        

        'date'
    ];
    
}
