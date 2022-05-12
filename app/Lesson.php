<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $table = 'lessons';

    protected $fillable = [
        'title',
        'course_id',
        'static_content',
        'audio_file',
        'video_link',
        'video_file',
    ];

    protected $appends = ['audio_file_real_path', 'video_full_url'];


    public function getAudioFileRealPathAttribute()
    {
        if ($this->audio_file) {
            return asset("/storage/{$this->audio_file}");
        } else {
            return null;
        }
    }


    public function getVideoFullUrlAttribute()
    {
        if ($this->video_file) {
            return asset("/storage/{$this->video_file}");
        } else {
            return null;
        }
    }


    public function course()
    {
        return $this->belongsTo( Course::class );
    }

    /**
     * Get the essays for the lesson.
     */
    public function essays()
    {
        return $this->hasMany('App\Essay');
    }
}
