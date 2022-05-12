<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plagiarism extends Model
{
    protected $table = 'plagiarism';

    protected $fillable = [

        'checked_essay_id',

        'matched_essay_id',

        'percentage',
    ];


    public function checked_essay(){
        return $this->hasMany('App\EssayAnswer', 'checked_essay_id');
    }


    public function matched_essay(){
        return $this->hasMany('App\EssayAnswer', 'matched_essay_id');
    }
}
