<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Images extends Model
{
    protected $table = 'images';
     
    protected $fillable = [
        'id',
        'title',
    ];

     protected $hidden = [

        'image_url'
    ];
    protected $appends = ['image_full_url'];

     public function getImageFullUrlAttribute()



    {



        if ($this->image_url) {



            return asset("/storage/{$this->image_url}");



        } else {



            return null;



        }



    }
    
}
