<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bonuses extends Model
{
    protected $table = 'bonuses';
    
    protected $fillable = [
        'company_id',
        'full_name',
        'date',
        'bonus',
        'title'
    ];
}
