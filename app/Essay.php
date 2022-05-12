<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Essay extends Model
{
    protected $table = 'essays';
    protected $fillable = [
        'title',
        'essay_type',
        'question',
        'course_id',
        'lesson_id',
        'essay_image',
    ];

    public function course()
    {
        return $this->belongsTo( Course::class );
    }

    public function lesson()
    {
        return $this->belongsTo( Lesson::class );
    }

    /**
     * Get the answers for the essay.
     */
    public function answers()
    {
        return $this->hasMany('App\EssayAnswer');
    }

    /**
     * Get the answers for the essay.
     */
    public function latestAnswer()
    {
        return $this->hasOne('App\EssayAnswer')->latest();
    }
    protected $appends = ['image_full_url'];
    public function getImageFullUrlAttribute()
    {
        if ($this->essay_image) {
            return asset("/storage/{$this->essay_image}");
        } else {
            return null;
        }
    }
}
