<?php


namespace App\Exam;
use App\Http\Resources\ExamResult\ExamResultResource;
use App\StudentExam;
class ExamResult
{
    /** Student rexam result
     * @param $student_exams
     * @return mixed
     */
    public function examResultDetail($student_exams_id)
    {
        $query = StudentExam::with('parentQuestions.children')->find($student_exams_id);
        return new ExamResultResource($query);
    }

}
