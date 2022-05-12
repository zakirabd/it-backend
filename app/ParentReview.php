<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ParentReview extends Model
{
    protected $guarded=[];

    protected $appends = ['ratingSum'];


    public function teacher(){

        return $this->belongsTo(User::class,'teacher_id');
    }

    public function parent(){

        return $this->belongsTo(User::class,'user_id');
    }

    public function comment()
    {
        return $this->hasMany(ParentReviewComment::class)->with('user');
    }

    public function parentReview()
    {
        return $this->hasMany(ParentReview::class, 'teacher_id','teacher_id');
    }
    public function getratingSumAttribute()
    {

       return $this->rating * 20;
    }


}
