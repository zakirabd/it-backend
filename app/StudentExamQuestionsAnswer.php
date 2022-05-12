<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentExamQuestionsAnswer extends Model
{
    protected $table = 'student_exam_question_answers';

    protected $fillable = [
        'student_exam_question_id',
        'title',
        'is_correct',
        'score',
    ];

    protected $appends = ['answer_full_url','correct_full_url'];

    public function getAnswerFullUrlAttribute()
    {
        if ($this->title) {
            return asset("/storage/{$this->title}");
        } else {
            return null;
        }
    }
    public function getCorrectFullUrlAttribute()
    {
        if ($this->is_correct) {
            return asset("/storage/{$this->is_correct}");
        } else {
            return null;
        }
    }
    public function scopeSumCustom($query,$student_exam_question_id)
    {

        return $query->whereIn('student_exam_question_id', $student_exam_question_id)->where('is_correct', 1)->sum("score");
    }

    public function matchingAnswered(){

        return $this->hasOne(StudentAnswer::Class,'answer_id','id')->with('givenAnswer');
    }

    public function matchingAnsweredId(){

        return $this->hasOne(StudentAnswer::Class,'matching_answer_id','id')->with('givenAnswer');
    }

    public function studentGivenAnswers()
    {
        return $this->belongsToMany(StudentExamQuestionsAnswer::class, 'student_answers', 'student_exam_question_id', 'answer_id');
    }

    public function dropdownGivenAnswer(){
        return $this->hasOne(StudentAnswer::Class,'answer_id','id');

    }

}
