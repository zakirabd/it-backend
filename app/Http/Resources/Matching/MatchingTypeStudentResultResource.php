<?php

namespace App\Http\Resources\Matching;

use Illuminate\Http\Resources\Json\JsonResource;

class MatchingTypeStudentResultResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $res = [
            'id'               => $this->id,
            'title'            => $this->title,
            'is_correct'       => $this->is_correct,
            'score'            => $this->score,
            'answer_full_url'  => $this->answer_full_url,
            'correct_full_url' => $this->correct_full_url,
            'given_answer'     => [],
        ];
        if ($this->matchingAnswered && $this->matchingAnswered->givenAnswer) {
            $res['given_answer'][] = new MatchingTypeGivenAnswerList($this->matchingAnswered->givenAnswer);
        }
        return $res;
    }
}
