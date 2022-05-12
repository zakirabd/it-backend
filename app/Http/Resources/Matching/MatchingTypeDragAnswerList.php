<?php

namespace App\Http\Resources\Matching;

use Illuminate\Http\Resources\Json\JsonResource;

class MatchingTypeDragAnswerList extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        if (!$this->matchingAnsweredId) {
            return [
                'id'               => $this->id,
                'is_correct'       => $this->is_correct,
                'correct_full_url' => $this->correct_full_url,
                'score'            => $this->score,
            ];
        }
    }
}
