<?php


namespace App\Exam\QusetionType;
use App\Interfaces\QuestionTypeInterface;
use App\StudentExamQuestions;
use App\StudentExamQuestionsAnswer;

class DropdownTypeQuestion implements QuestionTypeInterface
{
    /**
     * @param $question_answer
     * @return mixed
     */
    public function prepareAnswer($question_answer)
    {

        $given_answer_ids = collect($question_answer['student_exam_question_answers']);
        $main_array_set   = [];
        foreach ($given_answer_ids as $each_question_answer) {
            $given_answer_matching = StudentExamQuestionsAnswer::findOrFail($each_question_answer['id']);
            $main_array_set[$given_answer_matching->id] = [
                'is_correct' => $given_answer_matching->is_correct == $each_question_answer['given_answer']
                    ? 1
                    : 0,
                'score'      => $given_answer_matching->is_correct == $each_question_answer['given_answer']
                    ? $given_answer_matching->score
                    : 0,
                'answer'     => $each_question_answer['given_answer'],
            ];
            $this->save($question_answer['id'], $main_array_set);
        }
    }

    public function save($question_id, $main_array_set)
    {

        $question = StudentExamQuestions::findOrFail($question_id);
        $question->studentGivenAnswers()->sync($main_array_set);

    }

}
