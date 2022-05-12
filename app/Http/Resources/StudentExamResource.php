<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentExamResource extends JsonResource
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
            'id' => $this->id,
            'student_id' => $this->student_id,
            'exam_title'=>$this->exam_title,
            'start_time' => $this->start_time,
            'duration' => $this->duration,
            'end_time' => $this->end_time,
            'spend_time' => $this->spend_time,
            'score' => $this->score,
            'max_score' => $this->max_score,
            'is_submit' => $this->is_submit,
            'status' => $this->status,
            'student_exam_parent_questions' => StudentExamQuestionPartsResource::collection($this->parentQuestions),
        ];
    }
}
