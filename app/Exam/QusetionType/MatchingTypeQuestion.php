<?php

namespace App\Exam\QusetionType;
use App\Interfaces\QuestionTypeInterface;
use App\StudentExamQuestions;
use App\StudentExamQuestionsAnswer;

class MatchingTypeQuestion implements QuestionTypeInterface
{
    /**
     * question answers data formatted for store in DB
     *
     * @param $question_answer
     * @return string
     */
    public function prepareAnswer($question_answer)
    {

        $given_answer_ids = collect($question_answer['student_exam_question_answers']);
        $main_array_set   = [];
        foreach ($given_answer_ids as $each_question_answer) {
            $given_answer_matching = StudentExamQuestionsAnswer::findOrFail($each_question_answer['id']);
            if ($each_question_answer['given_answer'] && $each_question_answer['given_answer'][0]) {
                $main_array_set[$given_answer_matching->id] = [
                    'is_correct'         => $given_answer_matching->id
                                            == $each_question_answer['given_answer'][0]['id']
                        ? 1
                        : 0,
                    'score'              => $given_answer_matching->id
                                            == $each_question_answer['given_answer'][0]['id']
                        ? $given_answer_matching->score
                        : 0,
                    'answer'             => $each_question_answer['given_answer'][0]['is_correct'],
                    'matching_answer_id' => $each_question_answer['given_answer'][0]['id'],
                ];
                $this->save($question_answer['id'], $main_array_set);
            } else {
                $this->save($question_answer['id'], $main_array_set);
            }
        }
    }

    /** Student Answer store in db.
     * @param $question_id
     * @param $main_array_set
     */
    public function save($question_id, $main_array_set)
    {
        $question = StudentExamQuestions::findOrFail($question_id);
        $question->studentGivenAnswers()->sync($main_array_set);
    }
}
