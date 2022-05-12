<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Examlocked extends Model
{
    protected $table = 'exam_locked';

    protected $casts = [
        'timer_start' => 'datetime'
    ];

    protected $appends = [
        'has_unlock_remaining_time'
    ];

    protected $fillable = [
        'title',
        'student_id',
        'course_id',
        'lesson_id',
        'exam_id',
        'is_block',
        'retake_count',
        'timer_start'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function getHasUnlockRemainingTimeAttribute()
    {
        if (is_null($this->timer_start)) {
            return false;
        }

        return $this->timer_start->diffInSeconds() < ($this->exam->retake_minutes * 60);
    }
}
