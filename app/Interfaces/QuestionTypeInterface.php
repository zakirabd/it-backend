<?php


namespace App\Interfaces;


interface QuestionTypeInterface
{
    /**
     * @param $question_answer
     * @return mixed
     */
    public function prepareAnswer($question_answer);

    /**
     * @param $question_id
     * @param $main_array_set
     * @return mixed
     */
    public function save($question_id, $main_array_set);
}
