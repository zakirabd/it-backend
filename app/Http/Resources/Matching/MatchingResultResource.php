<?php

namespace App\Http\Resources\Matching;

use Illuminate\Http\Resources\Json\JsonResource;

class MatchingResultResource extends JsonResource
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
            'sub_type'                      => $this->sub_type,
            'title'                         => $this->title,
            'student_exam_question_answers' => MatchingTypeStudentResultResource::collection($this->answers)
        ];
    }

}
