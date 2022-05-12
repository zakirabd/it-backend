<?php


namespace App\Exam\QusetionType;
use App\Interfaces\QuestionTypeInterface;
use App\StudentExamQuestions;
use App\StudentExamQuestionsAnswer;

class CheckboxTypeQuestion implements QuestionTypeInterface
{
    /**
     * question answers data formatted for store in DB
     *
     * @param $question_answer
     * @return string
     */
    public function prepareAnswer($question_answer)
    {
        $given_answer_ids = collect($question_answer['given_answer'])->take(2);
        $main_array_set   = [];
        foreach ($given_answer_ids as $each_question_answer) {
            $given_answer_matching = StudentExamQuestionsAnswer::findOrFail($each_question_answer['id']);
            $main_array_set[$given_answer_matching->id] = [
                'is_correct' => $given_answer_matching->is_correct,
                'score'      => $given_answer_matching->score
            ];
        }
        $this->save($question_answer['id'], $main_array_set);
    }

    /** student Answer store in db.
     * @param $question_id
     * @param $main_array_set
     */
    public function save($question_id, $main_array_set)
    {
        $question = StudentExamQuestions::findOrFail($question_id);
        $question->studentGivenAnswers()->sync($main_array_set);
    }

}
