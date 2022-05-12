<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentNote extends Model
{
    protected $fillable=[
        'title',
        'description',
        'created_at',
        'date',
        'student_id'
    ];
}
