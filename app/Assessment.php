<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
//    public $timestamps = false;
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function user(){
        return $this->belongsTo(User::class);
    }


    public function staff(){
        return $this->belongsTo(User::class,'staff_id');
    }

}
