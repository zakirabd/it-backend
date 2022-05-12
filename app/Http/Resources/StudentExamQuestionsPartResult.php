<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentExamQuestionsPartResult extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'title'  => $this->title,
            'out_of' => round($this->childrenQuestions->sum('question_score')),
            'points' => round($this->childrenQuestions->sum('student_sum_score_given_answers'))
        ];
    }
}
