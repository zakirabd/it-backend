<?php

namespace App\Http\Resources\Dropdown;

use Illuminate\Http\Resources\Json\JsonResource;

class DropdownTypeStudentAnswerResource extends JsonResource
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
            'id'               => $this->id,
            'title'            => $this->title,
            'answer_full_url'  => $this->answer_full_url,
            'correct_full_url' => $this->correct_full_url,
            'given_answer'     => $this->formatGivenAnswer($this->dropdownGivenAnswer)
        ];
    }

    public function formatGivenAnswer($dropdownGivenAnswer)
    {
        $given_answer = '';
        if ($dropdownGivenAnswer) {
            $given_answer = $dropdownGivenAnswer->answer;
        }
        return $given_answer;
    }
}
