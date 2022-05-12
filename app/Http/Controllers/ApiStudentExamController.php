<?php

namespace App\Http\Controllers;
use App\Exam;
use App\Http\Resources\ExamResult\ExamResultPartQuestionResource;
use App\Http\Resources\StudentExamQuestionPartsResource;
use App\StudentExam;
use App\StudentExamQuestions;
use App\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ApiStudentExamController extends Controller
{

    /**
     * @param Exam $exam
     * @param User $user
     * @return \Illuminate\Support\Collection
     */
    public function getStudentExamParentQuestion(Exam $exam, User $user)
    {
        $studentExam = StudentExam::with('exam')
            ->where('student_id', $user->id)
            ->whereNull('is_submit')
            ->where('exam_id', $exam->id)
            ->first();

        if (empty($studentExam)) {
            $studentExam = StudentExam::with('exam')
                ->where('student_id', $user->id)
                ->where('exam_id', $exam->id)
                ->orderBy('id', 'desc')
                ->first();
        }
        return StudentExamQuestions::with('answers', 'studentAnswers')
            ->where('student_exam_id', $studentExam->id)
            ->whereNull('parent_id')
            ->orderBy('id', 'ASC')
            ->pluck('id');
    }

    /**
     * @param StudentExamQuestions $studentExamQuestions
     * @return StudentExamQuestionPartsResource
     */
    public function getStudentExamQuestions(StudentExamQuestions $studentExamQuestions)
    {
        return new StudentExamQuestionPartsResource($studentExamQuestions);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function StudentExam(Request $request)
    {
        try {
            $studentExam = StudentExam::with('exam')
                ->where('exam_id', $request->exam_id)
                ->where('student_id', $request->student_id)
                ->whereNull('is_submit')
                ->firstOrFail();

            $studentExam->timeZone = config('app.timezone');
            return response()->json($studentExam);
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['msg' => 'Exam already taken'], 404);

        }

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function studentHomework(Request $request){
        try {
            $studentExam = StudentExam::with('exam')
                ->where('exam_id', $request->exam_id)
                ->where('student_id', $request->student_id)
                ->whereNull('is_submit')
                ->first();

            if (empty($studentExam)) {
                $studentExam = StudentExam::with('exam')
                    ->where('student_id', $request->student_id)
                    ->where('exam_id', $request->exam_id)
                    ->orderBy('id', 'desc')
                    ->first();
            }

            $studentExam->timeZone = config('app.timezone');
            return response()->json($studentExam);
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['msg' => 'Home Work  already taken'], 404);

        }
    }

    /**
     * Student question answer save
     *
     * @param Request $request
     * @return array
     */
    public function studentExamSave(Request $request)
    {
        (new Exam\ExamSubmit($request))->prepareAnswer();
        return [
            'msg'  => 'Answer save successfully',
            'data' => $this->getStudentExamQuestions(StudentExamQuestions::findOrFail($request->id))
        ];
    }

    public function studentExamSubmit(Request $request)
    {
        return (new Exam\ExamAfterSubmit($request))->examSubmit();
    }

    public function StudentHomeWorkSubmit(Request $request)
    {

        return (new Exam\ExamAfterSubmit($request))->homeWorkSubmit();
    }


    public function studentExamResultParentIds(StudentExam $student_exam)
    {
        $patient_ids = $student_exam->parentQuestions()->pluck('id');
        return response()->json([
            'parent_ids'  => $patient_ids,
            'student_exam' => $student_exam,
        ]);

    }

    public function studentExamResultPartDetail(StudentExamQuestions $studentExamQuestions)
    {
        return new ExamResultPartQuestionResource($studentExamQuestions);
    }

    public function studentExamResultDetail($student_exams)
    {

        return (new Exam\ExamResult())->examResultDetail($student_exams);
    }

}
