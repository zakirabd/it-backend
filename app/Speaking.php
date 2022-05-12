<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Speaking extends Model
{
    protected $table = 'speakings';
    protected $fillable = [
        'title',
        'speaking_type',
        'question',
        'course_id',
        'lesson_id',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
    public function answare()
    {
        return $this->hasMany(SpeakingAnswer::class);
    }
    public function reviews()
    {
        return $this->hasMany(SpeakingReviews::class);
    }
}

