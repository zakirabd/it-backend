<?php


namespace App\Exam;

use App\Exam\QusetionType\DropdownTypeQuestion;
use App\Exam\QusetionType\MatchingTypeQuestion;
use App\Exam\QusetionType\RadioTypeQuestion;
use App\Exam\QusetionType\CheckboxTypeQuestion;

class ExamSubmit
{
    public array $question_type_radio = [
        'single_choice',
        'single_image_type',
        'single_image_choice',
        'single_text_type',
        'single_sat_type',
        'single_video_type',
        'single_audio_type',
        'single_audio_type_with_image',
        'single_audio_with_image',
        'single_boolean_type',

    ];

    public array $question_answers = [];

    public array $question_type_checkbox = [
        'multiple_choice'
    ];

    public array $question_type_matching  = [
        'matching_type'
    ];
    public array $question_type_droppdown = [
        'dropdown_question_type'
    ];

    public function __construct($question_answers)
    {
        $this->question_answers = $question_answers->student_exam_questions;
    }

    /**
     * Prepare answer for store in db
     */
    public function prepareAnswer()
    {

        foreach ($this->question_answers as $question_answer) {

            if (in_array($question_answer['question_type'], $this->question_type_matching)) {
                (new MatchingTypeQuestion())->prepareAnswer($question_answer);
            }
            if (in_array($question_answer['question_type'], $this->question_type_droppdown)) {
                (new DropdownTypeQuestion())->prepareAnswer($question_answer);
            }
            if (!empty($question_answer['given_answer'])) {
                if (in_array($question_answer['question_type'], $this->question_type_radio)) {
                    (new RadioTypeQuestion())->prepareAnswer($question_answer);
                }
                if (in_array($question_answer['question_type'], $this->question_type_checkbox)) {

                    (new CheckboxTypeQuestion())->prepareAnswer($question_answer);
                }
            }

        }
        return response()->json(['msg' => 'Answer saved  successfully'], 200);
    }
}
