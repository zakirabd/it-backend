<?php



namespace App;



use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Notifications\Notifiable;

use Laravel\Passport\HasApiTokens;

use Illuminate\Support\Facades\DB;



class User extends Authenticatable

{

    use HasApiTokens, Notifiable;



    /**

     * The attributes that are mass assignable.

     *

     * @var array

     */

    protected $fillable = [

        'first_name',

        'last_name',

        'email',

        'password',

        'phone_number',

        'avatar_url',

        'role',

        'payment_Reminder',

        'date_of_birth',

        'company_id',

        'lang',

        'send_email_status',

        'attendance_lock_status'

    ];



    /**

     * The attributes that should be hidden for arrays.

     *

     * @var array

     */

    protected $hidden = [

        'password', 'remember_token', 'avatar_url'

    ];



    /**

     * The attributes that should be cast to native types.

     *

     * @var array

     */

    protected $casts = [

        'email_verified_at' => 'datetime',

    ];



    /**

     * The accessors to append to the model's array form.

     *

     * @var array

     */

    protected $appends = ['full_name', 'avatar_full_url', 'lock_status', 'parentChildren', 'teacher'];



    /**

     * Get the user's full name.

     *

     * @return string

     */

    public function getFullNameAttribute()

    {

        return "{$this->first_name} {$this->last_name}";

    }

    public function getTeacherAttribute(){

        $sql = DB::table('teacher_enrolls')

                ->join('users', 'teacher_enrolls.teacher_id','=','users.id')

                ->where('teacher_enrolls.student_id', $this->id)->where('teacher_enrolls.status', '1')->select('teacher_id','first_name', 'last_name', 'email', 'phone_number', 'date_of_birth');





        return $sql->get();

    //    getStudentTeacher

    }

    public function getAvatarFullUrlAttribute()

    {

        if ($this->avatar_url) {

            return asset("/storage/{$this->avatar_url}");

        } else {

            return null;

        }

    }

    // get parents children

    public function getParentChildrenAttribute(){



        $sql = DB::table('parent_student')

                ->join('users', 'parent_student.student_id','=','users.id')

                ->where('parent_student.parent_id', $this->id)->select('id', 'first_name', 'last_name', 'email', 'phone_number', 'date_of_birth');





        return $sql->get();

    }



    /**

     * Get the company of the user.

     */

    public function company()

    {

        return $this->belongsTo('App\Company');

    }



    /**

     * Get the parent of the user.

     */

    public function parent()

    {

        return $this->belongsToMany('App\User', 'parent_student', 'student_id', 'parent_id');

    }

     public function child()

    {

        return $this->belongsToMany('App\User', 'parent_student', 'parent_id', 'student_id');

    }

    

    public function parentEmail()

    {

        return $this->belongsToMany('App\User', 'parent_student', 'student_id', 'parent_id')->pluck('email');

    }



    public function getStudentTeacherList()

    {

        return $this->belongsToMany('App\User', 'teacher_enrolls', 'student_id', 'teacher_id');

    }



    /**

     * Get the children of the user.

     */

    public function children()

    {

        return $this->belongsToMany('App\User', 'parent_student', 'parent_id', 'student_id');

    }



    /**

     * Get the courses of the user.

     */

    public function courses()

    {

        return $this->belongsToMany('App\Course');

    }



    /**

     * Get the teachers of the user.

     */

    public function teachers()

    {

        return $this->hasMany('App\TeacherEnroll', 'student_id');

    }



    /**

     * Get the students of the user.

     */

    public function students()

    {

        return $this->hasMany(TeacherEnroll::class, 'teacher_id');

    }



    public function teacherStudents()

    {

        return $this->hasMany(TeacherEnroll::class, 'teacher_id');

    }



    /**

     * Get the answers of the user.

     */

    public function answers()

    {

        return $this->hasMany('App\EssayAnswer');

    }



    public function speakingAnswers()

    {

        return $this->hasMany('App\SpeakingAnswer');

    }



    /**

     * Get the attendances of the user.

     */

    public function attendances()

    {

        return $this->hasMany('App\Attendance');

    }



    public function class_count()

    {

        return $this->hasMany('App\Attendance')

            ->where('paid', 0);

    }



    public function studentAttendance()

    {

        return $this->hasMany('App\Attendance')

            ->where('paid', 0);

    }



    public function getLockStatusAttribute()

    {



        $allClass     = [];

        $classChecked = false;

        foreach ($this->class_count->groupBy('teacher_id') as $class) {

            $allClass[]   = $class->count() >= $this->payment_Reminder_max_value;

            $classChecked = $class->count() >= $this->payment_Reminder_max_value;

            if ($classChecked === true) break;

        }

        return $classChecked;

    }





    public function teacherAttendances()

    {

        return $this->hasMany(Attendance::class, 'teacher_id');



    }



    public function teacherLock()

    {

        return $this->hasOne(TeacherLock::class, 'teacher_id');

    }



    /** teacher lock

     * @return \Illuminate\Database\Eloquent\Relations\HasOne

     */

    public function locked_teacher()

    {

        return $this->hasOne(TeacherLock::class, 'teacher_id')->where('lock_status', 1);

    }



    /** get parent review

     * @return \Illuminate\Database\Eloquent\Relations\HasMany

     */

    public function parentReview()

    {

        return $this->hasMany(ParentReview::Class, 'teacher_id');

    }



    /** Lesson assign to student

     * @return \Illuminate\Database\Eloquent\Relations\HasMany

     */

    public function studentEnrollClass()

    {

        return $this->hasMany(TeacherEnroll::class, 'student_id', 'id')->where('status', '1');

    }



    /** get unlock student only

     * @param $query

     * @param $value

     * @return mixed

     */

    public function scopeUnlockStudent($query, $value)

    {



        return $query->where('role', 'student')

            ->where('company_id', $value)

            ->where('manual_lock_status', 0)

            ->where('attendance_lock_status', 0);

    }



    /** get all company student

     * @param $query

     * @param $company_id

     * @return mixed

     */

    public function scopeCompanyAllStudent($query, $company_id)

    {

        return $query->where(['company_id' => $company_id, 'role' => 'student'])->select(

            'id',

            'first_name',

            'last_name',

            'payment_Reminder_max_value',

            'payment_Reminder_min_value'

        );

    }



    /** student certificates

     * @return \Illuminate\Database\Eloquent\Relations\HasMany

     */

    public function certificates(): \Illuminate\Database\Eloquent\Relations\HasMany

    {

        return $this->hasMany(Advanced::class, 'student_id', 'id')->select('id', 'student_id','course_type','score','certificate');

    }

}

