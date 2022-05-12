<?php

namespace App\Http\Resources;

use App\Http\Resources\Matching\QuestionsResource as MatchingQuestionResource;
use App\Http\Resources\Multiple\QuestionsResource as MultipleQuestionResource;
use App\Http\Resources\Single\QuestionsResource as SingleQuestionResource;
use App\Http\Resources\Dropdown\QuestionsResource as DropdownQuestionResource;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentExamQuestionsTypeResource extends JsonResource
{

    /**
     * define class for question type
     */
    public const question_type = [
        'matching_type'                => MatchingQuestionResource::class,
        'multiple_choice'              => MultipleQuestionResource::class,
        'single_choice'                => SingleQuestionResource::class,
        'single_image_type'            => SingleQuestionResource::class,
        'single_image_choice'          => SingleQuestionResource::class,
        'single_text_type'             => SingleQuestionResource::class,
        'single_sat_type'              => SingleQuestionResource::class,
        'single_video_type'            => SingleQuestionResource::class,
        'single_audio_type'            => SingleQuestionResource::class,
        'single_audio_type_with_image' => SingleQuestionResource::class,
        'single_boolean_type'          => SingleQuestionResource::class,
        'single_audio_with_image'      => SingleQuestionResource::class,
        'dropdown_question_type'       => DropdownQuestionResource::class,
    ];

    /**
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function toArray($request)
    {
        $resourceCLass = self::question_type[$this->question_type];
        return new $resourceCLass($this);

    }
}
