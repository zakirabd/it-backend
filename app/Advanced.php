<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Advanced extends Model
{
    protected $table = 'advanceds';
    protected $fillable = [
        'student_id',
        'exam_id',
        'date',
        'course_type',
        'score',
        'title',
        'program',
        'year',
        'scholarship',
        'certificate'

    ];
    protected $appends = ['certificate_full_url'];
    public function getcertificateFullUrlAttribute()
    {
        if ($this->certificate) {
            return asset("/storage/pdf/{$this->certificate}");
        } else {
            return null;
        }

    }

}
