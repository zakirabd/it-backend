<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentExamQuestionPartsResource extends JsonResource
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
            'status'                 => $this->getParentStatus($this->children),
            'points'                 => $this->childrenQuestions->sum('student_sum_score_given_answers'),
            'id'                     => $this->id,
            'title'                  => $this->title,
            'question_description'   => $this->question_description,
            'question_type'          => $this->question_type,
            'image_full_url'         => $this->image_full_url,
            'audio_full_url'         => $this->audio_full_url,
            'video_link'             => $this->video_link,
            'video_file'             => $this->video_file,
            'video_full_url'         => $this->video_full_url,
            'student_exam_questions' => StudentExamQuestionsTypeResource::collection($this->children)
        ];
    }

    private function getParentStatus($children)
    {
        $total_questions     = 0;
        $total_given_answers = 0;
        foreach ($children as $child) {
            if ($child->question_type == 'matching_type') {
                $total_questions     += count($child->answers);
                $total_given_answers += count($child->studentGivenAnswerHasMatching);
            } else {
                $total_questions++;
                count($child->studentGivenAnswers) > 0
                    ? $total_given_answers++
                    : '';
            }
        }
        return $this->calulateStatus($total_questions, $total_given_answers);
    }

    private function calulateStatus($total_questions, $total_given_answers)
    {
        $parent_status = 'Unanswered';
        if ($total_questions == $total_given_answers) {
            $parent_status = 'Answered';
        } else if ($total_given_answers > 0) {
            $parent_status = 'Partially Answered';
        }
        return $parent_status;
    }
}
