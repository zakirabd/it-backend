<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentExamQuestions extends Model
{
    protected $table = 'student_exam_questions';

    protected $fillable = [
        'student_exam_id',
        'question_id',
        'question',
        'question_type',
        'sub_type',
        'parent_id',
        'sort_id',
        'question_score',
        'question_description',
        'description',
        'question_image',
        'audio_file',
        'video_link',
        'video_file',
    ];
    protected $appends  = ['image_full_url', 'audio_full_url', 'video_full_url', 'student_sum_score_given_answers'];

    public function answers()
    {
        return $this->hasMany(StudentExamQuestionsAnswer::class, 'student_exam_question_id', 'id');
    }


    public function isCorrectAnswers()
    {
        return $this->hasMany(StudentExamQuestionsAnswer::class, 'student_exam_question_id', 'id')->where('is_correct', 1);
    }

    public function studentAnswers()
    {
        return $this->hasMany(StudentAnswer::class, 'student_exam_question_id', 'id');

    }

    public function studentGivenAnswers()
    {
        return $this->belongsToMany(StudentExamQuestionsAnswer::class, 'student_answers', 'student_exam_question_id', 'answer_id');
    }

    public function studentGivenAnswerHasMatching()
    {
        return $this->belongsToMany(StudentExamQuestionsAnswer::class, 'student_answers', 'student_exam_question_id', 'answer_id')
            ->whereNotNull('matching_answer_id');
    }

    public function getStudentSumScoreGivenAnswersAttribute()
    {
        return $this->studentCorrentAnswer()->sum('score');
    }


    public function childrenQuestions()
    {
        return $this->hasMany(self::class, 'parent_id')->with('studentGivenAnswers');
    }


    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')->with('studentCorrentAnswer');

    }


    public function studentCorrentAnswer()
    {
        return $this->hasMany(StudentAnswer::class, 'student_exam_question_id', 'id')->where('is_correct', 1);
    }


    public function exam()
    {
        return $this->belongsTo(StudentExam::class, 'student_exam_id', 'id');
    }

    public function correctAnswer()
    {
        return $this->hasMany(StudentExamQuestionsAnswer::class)->where('is_correct', 1);
    }


    public function getImageFullUrlAttribute()
    {

        if ($this->question_image) {
            return asset("/storage/{$this->question_image}");
        } else {
            return null;
        }
    }

    public function getAudioFullUrlAttribute()
    {
        if ($this->audio_file) {
            return asset("/storage/{$this->audio_file}");
        } else {
            return null;
        }
    }

    public function getVideoFullUrlAttribute()
    {
        if ($this->video_file) {
            return asset("/storage/{$this->video_file}");
        } else {
            return null;
        }
    }


}
