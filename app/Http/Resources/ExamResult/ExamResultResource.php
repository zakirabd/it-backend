<?php
namespace App\Http\Resources\ExamResult;
use Illuminate\Http\Resources\Json\JsonResource;
class ExamResultResource extends JsonResource
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
            'exam_title'=>$this->exam_title,
            'student_exam_parent_questions' => ExamResultPartQuestionResource::collection($this->parentQuestions),
        ];
    }
}
