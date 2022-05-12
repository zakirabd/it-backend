<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SpeakingReviews extends Model
{
    protected $table = 'speaking_reviews';
    protected $fillable = [
        'review',
        'is_student',
        'speaking_answer_id',
        'user_id',
        'grade',
        'rating'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
