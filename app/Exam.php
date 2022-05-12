<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $table = 'exams';
    protected $fillable = [
        'title',
        'duration_minutes',
        'description',
        'course_id',
        'lesson_id',
        'retake_minutes',
        'retake_time',
        'points',
        'exam_image',
    ];
    protected $appends = ['image_full_url'];

    public function getImageFullUrlAttribute()
    {
        if ($this->exam_image) {
            return asset("/storage/{$this->exam_image}");
        } else {
            return null;
        }
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     *get student exam
     */
    public function studentExam($user_id)
    {
        $query = $this->hasOne(StudentExam::class)->where('student_id', $user_id)->whereNull('is_submit')->first();
        if (empty($query)) {
            $query = $this->hasOne(StudentExam::class)->where('student_id', $user_id)->orderBy('id', 'desc')->first();
        }
        return $query;
    }
}
