<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ParentReviewComment extends Model
{
     protected $guarded=[];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
