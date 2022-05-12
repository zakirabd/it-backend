<?php

namespace App\Http\Resources\Single;

use App\Http\Resources\StudentAnswerResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class SingleResultResource extends JsonResource
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
            'image_full_url'                => $this->image_full_url,
            'title'                         => $this->title,
            'given_answer'                  => $this->givenAnswerData($this->studentGivenAnswers),
            'student_exam_question_answers' => SingleTypeResultResource::collection($this->isCorrectAnswers)
        ];
    }

    /**
     * return question type related data
     *
     * @param $studentGivenAnswers
     * @param $question_type
     * @return array|AnonymousResourceCollection|string
     */
    private function givenAnswerData($studentGivenAnswers)
    {
        if (count($studentGivenAnswers) > 0) {
            $given_answer = new SingleTypeResultResource($studentGivenAnswers[0]);
        } else {
            $given_answer = '';
        }
        return $given_answer;
    }
}
