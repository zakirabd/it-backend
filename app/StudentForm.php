<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentForm extends Model
{
     protected $table = 'student_form';

    
    protected $fillable = [



        'company_id',


        'date',



        'first_name',



        'last_name',



        'email',


        'phone_number',



        'date_of_birth',



        'parent_first_name',



        'parent_last_name',



        'parent_date_of_birth',



        'parent_email',



        'current_education',



        'education_center',



        'class_course',



        'faculty',


        'specialty',


        'gpa',


        'language_certification',


        'education_type',


        'country',


        'next_specialty',


        'budget',


        'source',


        'other_source',


        'education_financing',


        'created_at'



    ];
}
