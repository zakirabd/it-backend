<?php

namespace App\Http\Resources\Dropdown;

use Illuminate\Http\Resources\Json\JsonResource;

class DropdownResultResource extends JsonResource
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
            'student_exam_question_answers' => DropdownTypeStudentResultResource::collection($this->answers)
        ];
    }

}
