<?php



namespace App;



use Illuminate\Database\Eloquent\Model;



class TeacherEnroll extends Model

{

    protected $table = 'teacher_enrolls';



    protected $fillable = [

        'student_id',

        'study_mode',

        'teacher_id',

        'lesson_mode',

        'lesson_houre',

        'student_group_id',

        'fee'

    ];



    public function teacher()

    {

        return $this->belongsTo(User::class);

    }

    public function student()


    {


        return $this->belongsTo(User::class);


    }


}

