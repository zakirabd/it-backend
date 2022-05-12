<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $table = 'courses';

    protected $fillable = [
        'title',
        'level',
        'grade',
        'image_url',
    ];

    protected $appends = ['image_full_url'];

    public function getImageFullUrlAttribute()
    {
        if ($this->image_url) {
            return asset("/storage/{$this->image_url}");
        } else {
            return null;
        }
    }

    /**
     * Get the lessons for the course.
     */
    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    /**
     * Get the listenings for the course.
     */
    public function listenings()
    {
        return $this->hasMany(Listening::class)->orderBy('title');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function speakings()
    {
        return $this->hasMany(Speaking::class);
    }

    public function courseAssign()
    {
        return $this->hasOne(CourseAssign::class);
    }
}
