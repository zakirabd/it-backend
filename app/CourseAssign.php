<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CourseAssign extends Model
{
    protected $table = 'course_assigns';
    protected $fillable = [
        'companie_id',
        'course_id',
    ];
    public function company()
    {
        return $this->belongsTo( Company::class ,'companie_id','id');
    }
    public function course()
    {
        return $this->belongsTo( Course::class );
    }

}
