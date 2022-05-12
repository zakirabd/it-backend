<?php



namespace App;



use Illuminate\Database\Eloquent\Model;



class EssayAnswer extends Model

{

    /**

     * The attributes that are mass assignable.

     *

     * @var array

     */

    protected $fillable = [

        'answer',

        'is_submitted',

        'submit_date',

        'is_closed',

        'grade',

        'essay_id',

        'user_id',

    ];



    /**

     * Get the essay that owns the answer.

     */

    public function essay()

    {

        return $this->belongsTo('App\Essay');

    }



    public function plagiarism(){
        return $this->hasMany('App\Plagiarism', 'checked_essay_id');
    }

    /**

     * Get the user that owns the answer.

     */

    public function user()

    {

        return $this->belongsTo('App\User');

    }



    public function teachers()

    {

        return $this->hasMany(TeacherEnroll::class, 'student_id', 'user_id')->where('status', '1');

    }



    public function reviewsid()

    {

        return $this->hasMany(EssayReview::class);

    }



    /**

     * Get the reviews for the essay.

     */

    public function reviews()

    {

        return $this->hasMany(EssayReview::class)->with('user');

    }



    /**

     * Get the answers for the essay.

     */

    public function latestReview()

    {

        return $this->hasOne('App\EssayReview')->latest();

    }









}

