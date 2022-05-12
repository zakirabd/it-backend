<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentAnswer extends Model
{
    protected $table    = 'student_answers';
    protected $fillable = [
        'student_exam_question_id',
        'answer_id',
        'matching_answer_id',
        'answer',
        'is_correct',
        'score',
    ];
    protected $appends  = ['answer_full_url'];

    public function getAnswerFullUrlAttribute()
    {
        if ($this->answer) {
            return asset("/storage/{$this->answer}");
        } else {
            return null;
        }
    }

    public function scopeSumCustom($query, $student_exam_question_id)
    {

        return $query->whereIn('student_exam_question_id', $student_exam_question_id)->where('is_correct', 1)->sum("score");
    }

    public function givenAnswer()
    {
        return $this->belongsTo(StudentExamQuestionsAnswer::class, 'matching_answer_id','id');
    }
}
