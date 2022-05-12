<?php

namespace App\Http\Resources\ExamResult;

use App\Http\Resources\StudentExamQuestionsResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamResultPartQuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'points' => round($this->childrenQuestions->sum('student_sum_score_given_answers')),
            'id' => $this->id,
            'title' => $this->title,
            'student_exam_questions' => ExamResultQuestionTypeResource::collection($this->children)
        ];
    }
}
