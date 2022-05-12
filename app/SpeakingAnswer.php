<?php



namespace App;



use Illuminate\Database\Eloquent\Model;



class SpeakingAnswer extends Model

{

    protected $table = 'speaking_answer';

    protected $fillable = [

        'answer',

        'speaking_id',

        'user_id',

        'status',

    ];

    protected $appends = ['image_full_url'];



    public function getImageFullUrlAttribute()

    {

        if ($this->answer) {

            return asset("/storage/{$this->answer}");

        } else {

            return null;

        }

    }

    public function speaking()

    {

        return $this->belongsTo(Speaking::class);

    }

    public function user()

    {

        return $this->belongsTo(User::class);

    }

    public function review()

    {

        return $this->hasMany(SpeakingReviews::class);

    }

    public function teachers()

    {

        return $this->hasMany(TeacherEnroll::class, 'student_id', 'user_id')->where('status', '1');

    }



}

