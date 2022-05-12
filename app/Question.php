<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $table = 'questions';
    protected $fillable = [
        'title',
        'exam_id',
        'description',
        'question_description',
        'type',
        'sub_type',
        'score',
        'question_image',
        'audio_file',
        'video_link',
        'video_file',
        'parent_id',
        'sort_id',
    ];
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
    public function correctAnswer()
    {
        return $this->hasMany(Answer::class)->where('is_correct',1);
    }


    protected $appends = ['image_full_url','audio_full_url','video_full_url'];

    public function getImageFullUrlAttribute()
    {
        if ($this->question_image) {
            return asset("/storage/{$this->question_image}");
        } else {
            return null;
        }
    }
    public function getAudioFullUrlAttribute()
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
}
