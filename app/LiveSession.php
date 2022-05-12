<?php

namespace App;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class LiveSession extends Model
{
    protected $table = 'live_sessions';

    protected $fillable = [
        'week_day',
        'start_time',
        'duration',
        'student_group_id',
        'study_mode',
        'timezone',
        'user_id',
        'company_id',
        'live_class_id',
        'brainchart_link'
    ];

    protected $appends = ['week_day_string'];

    public function getWeekDayAttribute($value)
    {
        return json_decode($value);
    }

    public function getWeekDayStringAttribute()
    {
        $weekDay = array_map(function ($item) {
            switch ($item) {
                case "0":
                    $item = "Sunday";
                    break;
                case "1":
                    $item = "Monday";
                    break;
                case "2":
                    $item = "Tuesday";
                    break;
                case "3":
                    $item = "Wednesday";
                    break;
                case "4":
                    $item = "Thursday";
                    break;
                case "5":
                    $item = "Friday";
                    break;
                default:
                    $item = "Saturday";
            }

            return $item;
        }, $this->week_day);

        return implode(', ', $weekDay);
    }

    public function students()
    {
        return $this->belongsToMany(\App\User::class);
    }

    public function group()
    {
        return $this->belongsTo(StudentGroup::class,'student_group_id','id');
    }

    /**
     * Get the user that owns the attendance.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
