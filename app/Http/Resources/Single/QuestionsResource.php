<?php



namespace App\Http\Resources\Single;



use App\Http\Resources\StudentAnswerResource;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

use Illuminate\Http\Resources\Json\JsonResource;



class QuestionsResource extends JsonResource

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

            'id'                            => $this->id,

            'title'                         => $this->title,

            'description'                   => $this->description,

            'question_type'                 => $this->question_type,

            'sub_type'                      => $this->sub_type,

            'image_full_url'                => $this->image_full_url,

            'audio_full_url'                => $this->audio_full_url,

            // 'video_full_url'                => $this->video_full_url,

            'video_link'                    => $this->video_link,

            'video_file'                    => $this->video_file,

            'given_answer'                  => $this->givenAnswerData($this->studentGivenAnswers),

            'student_exam_question_answers' => StudentAnswerResource::collection($this->answers)

        ];

    }



    /**

     * return question type related data

     *

     * @param $studentGivenAnswers

     * @param $question_type

     * @return array|AnonymousResourceCollection|string

     */

    private function givenAnswerData($studentGivenAnswers)

    {

        if (count($studentGivenAnswers) > 0) {

            $given_answer = new StudentAnswerResource($studentGivenAnswers[0]);

        } else {

            $given_answer = '';

        }

        return $given_answer;

    }

}

