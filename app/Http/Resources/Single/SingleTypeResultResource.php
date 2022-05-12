<?php

namespace App\Http\Resources\Single;

use Illuminate\Http\Resources\Json\JsonResource;

class SingleTypeResultResource extends JsonResource
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
            'is_correct'       => $this->is_correct,
            'score'            => $this->score,
            'answer_full_url'  => $this->answer_full_url,
            'correct_full_url' => $this->correct_full_url,

        ];

    }


}
