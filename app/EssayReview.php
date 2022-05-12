<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EssayReview extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];


    /**
     * Get the answer that owns the review.
     */
    public function answer()
    {
        return $this->belongsTo('App\EssayAnswer','essay_answer_id');
    }

    /**
     * Get the user that owns the review.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
