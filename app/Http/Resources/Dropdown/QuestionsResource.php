<?php

namespace App\Http\Resources\Dropdown;

use Illuminate\Http\Resources\Json\JsonResource;

class QuestionsResource extends JsonResource
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
            'student_exam_question_answers' => DropdownTypeStudentAnswerResource::collection($this->answers)
        ];
    }
}
