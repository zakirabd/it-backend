<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $table = 'answer';

    protected $appends = ['answer_full_url', 'correct_full_url'];

    public function getAnswerFullUrlAttribute()
    {
        if ($this->title) {
            return asset("/storage/{$this->title}");
        } else {
            return null;
        }
    }

    public function getCorrectFullUrlAttribute()
    {
        if ($this->is_correct) {
            return asset("/storage/{$this->is_correct}");
        } else {
            return null;
        }
    }

}
