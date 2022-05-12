<?php



namespace App\Exam;



use App\Examlocked;

use App\Question;

use App\Services\StudentExamService;

use App\StudentAnswer;

use App\StudentExam;

use App\StudentExamQuestions;

use Carbon\Carbon;



class ExamAfterSubmit

{

    private $exam_id;

    private $student_id;

    private $studentExam_id;

    private $date;

    private $score;



    /**

     * ExamAfterSubmit constructor.

     * @param $request

     */

    public function __construct($request)

    {

        $this->exam_id        = $request->exam_id;

        $this->student_id     = $request->student_id;

        $this->studentExam_id = $request->studentExam_id;

        $this->date           = $request->date;

        $this->score          = $request->score;

//        $this->student_exam_parent_questions = $request->student_exam_parent_questions;

    }



    /** student exam submit

     * @return \Illuminate\Http\JsonResponse

     */
    //  manual exam submit
    public function manualExamSubmit(){
        $x;
        $x = (new StudentExamService())->sendCertManual($this->studentExam_id, $this->date, $this->score);
        return $x;
    }


    public function examSubmit()

    {

        $student_exam            = StudentExam::find($this->studentExam_id);

        $checkExamPreviousSubmit = $this->checkExamPreviousSubmit($student_exam);

        if ($checkExamPreviousSubmit == 1) {

            return response()->json(['msg' => 'You have already submitted this exam.'], 201);

        }



        $maxScore   = $student_exam->calculateQuestionMaxScore->sum('score');

        $score      = $this->totalPartsScore();

        $spend_time = $this->spendTime($student_exam);



        $student_exam->update([

            'is_submit'  => 1,

            'max_score'  => $maxScore,

            'score'      => $score,

            'status'     => $score >= 70

                ? 1

                : 0,

            'spend_time' => $spend_time,

        ]);

        if ($score >= 70) {

            (new StudentExamService())->sendCert($this->studentExam_id);

        }

        $this->examLockAfterExam($student_exam);

        return response()->json(['msg' => 'Exam submit successfully'], 200);

    }



    /** Student Home Work Submit

     * @return \Illuminate\Http\JsonResponse

     */



    public function homeWorkSubmit()

    {

        $student_exam = StudentExam::find($this->studentExam_id);

        $maxScore     = $student_exam->calculateQuestionMaxScore->sum('score');

        $score        = $this->totalPartsScore();



        $student_exam->update([

            'score'     => $score,

            'max_score' => $maxScore,

            'status'    => $score >= 70

                ? 1

                : 0,

            'is_submit' => 1,

        ]);



        $this->examLockAfterExam($student_exam);



        return response()->json(['msg' => 'Home Work submit successfully'], 200);



    }



    /** check exam submit in previous

     * @param $student_exam

     * @return int

     */

    public function checkExamPreviousSubmit($student_exam)

    {



        if ($student_exam->is_submit == 1 && $student_exam->duration != 0) {

            return 1;

        } else {

            return 0;

        }

    }



    public function calculateQuestionMaxScore()

    {



        return Question::where('exam_id', $this->exam_id)->sum('score');

    }



    /** calculate spend time for student attend exam.

     * @param $student_exam

     * @return string

     */

    public function spendTime($student_exam)

    {

        $time = Carbon::parse($student_exam->created_at)->diffInMinutes(Carbon::now());



        return $time . " min";

    }



    /**

     * calculate question Parts score as score in student exam table

     * @return mixed

     */

    public function totalPartsScore()

    {

        return round(StudentExamQuestions::with('childrenQuestions')

            ->where('student_exam_id', $this->studentExam_id)->whereNull('parent_id')

            ->get()->map(function ($q) {

                return $q->childrenQuestions->sum('student_sum_score_given_answers');

            })->sum());

    }



    /** after student attend exam , lock that exam in exam locked db.

     * @param StudentExam $student_exam

     */

    public function examLockAfterExam(StudentExam $student_exam)

    {

        Examlocked::where('student_id', $this->student_id)->where('exam_id', $student_exam->exam_id)->update(['is_block' => 0]);

    }

}

