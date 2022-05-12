<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OfficeManagerBonus extends Model
{
    protected $table = 'office_manager_bonuses';





    protected $fillable = [


        'office_manager_id',

        'bonus',
        
        'title',

        'date',

        'company_id'
    ];
}
