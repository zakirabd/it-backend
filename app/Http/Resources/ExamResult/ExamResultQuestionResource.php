<?php

namespace App\Http\Resources\ExamResult;

use App\Http\Resources\StudentAnswerResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamResultQuestionResource extends JsonResource
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
            'id' => $this->id,
            'question_type'=>$this->question_type,
            'title' => $this->title,
            'given_answer' => $this->givenAnswerData($this->studentGivenAnswers, $this->question_type),
            'student_exam_question_answers' => StudentAnswerResource::collection($this->isCorrectAnswers)
        ];
    }

    /**
     * @param $studentGivenAnswers
     * @param $question_type
     * @return StudentAnswerResource|array|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|string
     */
    private function givenAnswerData($studentGivenAnswers, $question_type)
    {
        if (count($studentGivenAnswers) > 0) {
            if ($question_type == 'multiple_choice') {
                $given_answer = StudentAnswerResource::collection($studentGivenAnswers);
            } else {
                $given_answer = new StudentAnswerResource($studentGivenAnswers[0]);
            }
        } else if ($question_type == "multiple_choice" || $question_type == "dropdown_question_type") {
            $given_answer = [];
        } else {
            $given_answer = '';
        }
        return $given_answer;
    }
}
