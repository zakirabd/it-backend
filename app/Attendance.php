<?php



namespace App;



use Illuminate\Database\Eloquent\Model;



class Attendance extends Model

{

    /**

     * The attributes that are mass assignable.

     *

     * @var array

     */

    protected $fillable = [

        'date',

        'event',

        'lesson_mode',

        'teacher_id',

        'user_id',

        'manually_add',

        'paid',

        'amount',

        'note',

        'lesson_houre',

        'study_mode',

        'student_group_id'



    ];



    /**

     * Get the teacher that owns the attendance.

     */

    public function teacher()

    {

        return $this->belongsTo(User::class, 'teacher_id');

    }



    public function user()

    {

        return $this->belongsTo('App\User');

    }

    public function teacher_info()

    {

        return $this->belongsTo(User::class,'teacher_id');

    }



    // TODO : Remove unused Relation.

}

