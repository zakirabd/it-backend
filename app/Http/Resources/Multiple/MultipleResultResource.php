<?php

namespace App\Http\Resources\Multiple;

use App\Http\Resources\StudentAnswerResource;
use Illuminate\Http\Resources\Json\JsonResource;

class MultipleResultResource extends JsonResource
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
            'id'                            => $this->id,
            'question_type'                 => $this->question_type,
            'title'                         => $this->title,
            'given_answer'                  => $this->givenAnswerData($this->studentGivenAnswers),
            'student_exam_question_answers' => MultipleTypeResultResource::collection($this->isCorrectAnswers),
        ];
    }

    /**
     * @param $studentGivenAnswers
     * @param $question_type
     * @return StudentAnswerResource|array|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|string
     */
    private function givenAnswerData($studentGivenAnswers)
    {
        if (count($studentGivenAnswers) > 0) {
            $given_answer = MultipleTypeResultResource::collection($studentGivenAnswers);
        } else {
            $given_answer = [];
        }
        return $given_answer;
    }
}
