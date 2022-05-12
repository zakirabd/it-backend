<?php


namespace App\Services;


use App\Answer;
use App\Course;
use App\Essay;
use App\Exam;
use App\Examlocked;
use App\Lesson;
use App\Question;
use App\StudentExam;
use App\StudentExamQuestions;
use App\StudentExamQuestionsAnswer;
use App\User;
use Illuminate\Support\Facades\DB;

class QuestionService
{

    private $request;
    private $question;

    public function __construct($request)
    {
        $this->request = $request;
        $this->question = $this->getQuestion();
    }

    /**
     * Filter by staff role
     *
     * @param $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getQuestion()
    {
        // return 'super-admin';

        $dataArray = array();
//        $sql = Question::with('correctAnswer')->where('exam_id', $this->request->exam_id)->where('type', 'parent')->get();
        $sql = Question::with('correctAnswer')->where('exam_id', $this->request->exam_id)->whereNull('parent_id')->orderBy('id', 'ASC')->get();
        foreach ($sql as $key => $item) {
            $dataArray[$key] = $item;
            $getChild = Question::with('correctAnswer')->where('parent_id', $item->id)->orderBy('sort_id', 'ASC')->get();
            if (!empty($getChild)) {
                $dataArray[$key]->children = $getChild;
            } else {
                $dataArray[$key]->children = array();

            }
        }
        return $sql;
    }

    public function getStudentQuestion()
    {

        if (isset($this->request->exam_id)) {

            $dataArray = array();

            $studentExam = StudentExam::with('exam')
                ->where('student_id', $this->request->student_id)
                ->whereNull('is_submit')
                ->where('exam_id', $this->request->exam_id)
                ->first();

            if (empty($studentExam)) {
                $studentExam = StudentExam::with('exam')
                    ->where('student_id', $this->request->student_id)
                    ->where('exam_id', $this->request->exam_id)
                    ->orderBy('id', 'desc')
                    ->first();
            }
//            return [$studentExam->exam, $studentExam->id, $studentExam->exam_title];
            $sql = StudentExamQuestions::with('answers', 'studentAnswers')
                ->where('student_exam_id', $studentExam->id)
                ->whereNull('parent_id')
                ->orderBy('id', 'ASC')
                ->get();

            foreach ($sql as $key => $item) {

                $dataArray[$key] = $item;
                $getChild = StudentExamQuestions::with('answers', 'studentAnswers')
                    ->where('parent_id', $item->id)
                    ->orderBy('sort_id', 'ASC')
                    ->get();
                if (!empty($getChild)) {
                    foreach ($getChild as $key2 => $question) {
                        if ($question->question_type == 'matching_type') {
                            $temp_array = array();
                            foreach ($question->answers as $key3 => $item) {
                                $temp_array[$key3] = $item;
                            }
                            shuffle($temp_array);
                            $question->answers_data = $temp_array;
                        } else {
                            $question->answers_data = $question->answers;
                        }
                    }
                    $dataArray[$key]->children = $getChild;
                } else {
                    $dataArray[$key]->children = array();
                }
            }
            $student_exam['question'] = $sql;

            $student_exam['exam'] = $studentExam;

            return $student_exam;
        }
    }
}
