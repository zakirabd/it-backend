<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentExamQuestionsResource extends JsonResource
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
            'title'                         => $this->title,
            'description'                   => $this->description,
            'question_type'                 => $this->question_type,
            'sub_type'                      => $this->sub_type,
            'image_full_url'                => $this->image_full_url,
            'audio_full_url'                => $this->audio_full_url,
            'video_link'                    => $this->video_link,
            'video_file'                    => $this->video_file,
            'given_answer'                  => $this->givenAnswerData($this->studentGivenAnswers, $this->question_type),
            'student_exam_question_answers' => StudentAnswerResource::collection($this->answers)
        ];
    }

    /**
     * return question type related data
     *
     * @param $studentGivenAnswers
     * @param $question_type
     * @return array|AnonymousResourceCollection|string
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
