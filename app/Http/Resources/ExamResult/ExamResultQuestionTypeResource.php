<?php

namespace App\Http\Resources\ExamResult;

use App\Http\Resources\Dropdown\DropdownResultResource;
use App\Http\Resources\Matching\MatchingResultResource;
use App\Http\Resources\Multiple\MultipleResultResource;
use App\Http\Resources\Single\SingleResultResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamResultQuestionTypeResource extends JsonResource
{

    /**
     * define class for question type
     */
    public const question_type = [
        'multiple_choice'              => MultipleResultResource::class,
        'single_choice'                => SingleResultResource::class,
        'single_image_type'            => SingleResultResource::class,
        'single_image_choice'          => SingleResultResource::class,
        'single_text_type'             => SingleResultResource::class,
        'single_sat_type'              => SingleResultResource::class,
        'single_video_type'            => SingleResultResource::class,
        'single_audio_type'            => SingleResultResource::class,
        'single_audio_type_with_image' => SingleResultResource::class,
        'single_boolean_type'          => SingleResultResource::class,
        'single_audio_with_image'      => SingleResultResource::class,
        'dropdown_question_type'       => DropdownResultResource::class,
        'matching_type'                => MatchingResultResource::class,

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
