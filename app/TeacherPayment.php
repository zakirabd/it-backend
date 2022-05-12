<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TeacherPayment extends Model
{
    protected $fillable = [
        'class_type',
        'status',
        'number_of_class',
        'teacher_id',
        'company_id',
    ];

    /*
    get teacher info
    */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /*
       get company info
       */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', id);
    }
}
