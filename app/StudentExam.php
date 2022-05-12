<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentExam extends Model
{
    protected $table = 'student_exams';

    protected $fillable = [
        'student_id',
        'exam_id',
        'exam_title',
        'start_time',
        'duration',
        'end_time',
        'score',
        'max_score',
        'spend_time',
        'status',
        'is_submit'
    ];


    public function calculateQuestionMaxScore()
    {
        return $this->hasMany(Question::class, 'exam_id', 'exam_id');
    }


    public function questions()
    {
        return $this->hasMany(StudentExamQuestions::class);
    }


    public function parts()
    {
        return $this->hasMany(StudentExamQuestions::class, 'student_exam_id', 'id')->with(['children'])->whereNull('parent_id')->get();
    }


    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id', 'id');
    }

    public function examlocked()
    {
        return $this->belongsTo(Examlocked::class, 'exam_id', 'exam_id');
    }

    public function parentQuestions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StudentExamQuestions::class)
            ->whereNull('parent_id')
            ->orderBy('id', 'ASC');
    }

}
