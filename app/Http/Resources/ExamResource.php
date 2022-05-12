<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExamResource extends JsonResource
{

    public $user_id = '';

    public function __construct($resource, $user_id)
    {
        parent::__construct($resource);
        $this->user_id = $user_id;
    }


    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'               => $this->id,
            'title'            => $this->title,
            'duration_minutes' => $this->duration_minutes,
            'retake_minutes'   => $this->retake_minutes,
            'retake_time'      => $this->retake_time,
            'description'      => $this->description,
            'student_exam'     => new StudentExamResource($this->studentExam($this->user_id)),
        ];
    }
}
