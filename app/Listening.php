<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Listening extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'audio_file',
        'course_id',
        'lesson_id',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['audio_file_real_path'];

    public function getAudioFileRealPathAttribute()
    {
        if ($this->audio_file) {
            return asset("/storage/{$this->audio_file}");
        } else {
            return null;
        }
    }
}
