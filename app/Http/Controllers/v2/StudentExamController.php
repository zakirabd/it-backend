<?php



namespace App\Http\Controllers\v2;

use App\Exam;

use App\Http\Controllers\Controller;

use App\Http\Resources\ExamResult\ExamResultPartQuestionResource;

use App\Http\Resources\StudentExamQuestionPartsResource;

use App\StudentExam;

use App\StudentExamQuestions;

use App\User;

use Illuminate\Database\Eloquent\ModelNotFoundException;

use Illuminate\Http\Request;



class StudentExamController extends Controller

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
    // manually exam
    public function StudentManuallyExam(Request $request)
    {
        try {
            $studentExam = StudentExam::with('exam')
                ->where('exam_id', $request->exam_id)
                ->where('student_id', $request->student_id)
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

            $studentExam = StudentExam::where('exam_id', $request->exam_id)

                ->where('student_id', $request->student_id)

                ->whereNull('is_submit')

                ->select('id','student_id','exam_title','exam_id')

                ->first();



            if (empty($studentExam)) {

                $studentExam = StudentExam::where('student_id', $request->student_id)

                    ->where('exam_id', $request->exam_id)

                    ->orderBy('id', 'desc')

                    ->select('id','student_id','exam_title','exam_id')

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



    /**

     * @param Request $request

     * @return \Illuminate\Http\JsonResponse

     */

    public function studentExamSubmit(Request $request)

    {

        return (new Exam\ExamAfterSubmit($request))->examSubmit();

    }
    // manually exam submit for certificate

     public function studentExamManualSubmit(Request $request){

        return (new Exam\ExamAfterSubmit($request))->manualExamSubmit();
    }


    /**

     * @param Request $request

     * @return \Illuminate\Http\JsonResponse

     */

    public function StudentHomeWorkSubmit(Request $request)

    {



        return (new Exam\ExamAfterSubmit($request))->homeWorkSubmit();

    }



    /**

     * @param StudentExam $student_exam

     * @return \Illuminate\Http\JsonResponse

     */

    public function studentExamResultParentIds(StudentExam $student_exam)

    {

        $patient_ids = $student_exam->parentQuestions()->pluck('id');

        return response()->json([

            'parent_ids'  => $patient_ids,

            'student_exam' => $student_exam,

        ]);



    }



    /**

     * @param StudentExamQuestions $studentExamQuestions

     * @return ExamResultPartQuestionResource

     */

    public function studentExamResultPartDetail(StudentExamQuestions $studentExamQuestions)

    {

        return new ExamResultPartQuestionResource($studentExamQuestions);

    }



    /**

     * @param $student_exams

     * @return \App\Http\Resources\ExamResult\ExamResultResource|mixed

     */

    public function studentExamResultDetail($student_exams)

    {

        return (new Exam\ExamResult())->examResultDetail($student_exams);

    }

}

