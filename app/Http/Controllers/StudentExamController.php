<?php



namespace App\Http\Controllers;



use App\Assessment;

use App\EssayAnswer;

use App\Exam;

use App\Examlocked;

use App\Http\Resources\StudentExamQuestionsPartResult;

use App\Question;

use App\Services\StudentExamService;

use App\SpeakingAnswer;

use App\StudentAnswer;

use App\StudentExam;

use App\StudentExamQuestions;

use App\StudentExamQuestionsAnswer;

use Carbon\Carbon;

use Illuminate\Http\Request;



class StudentExamController extends Controller

{

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index(Request $request)

    {



        if ($request->type == 'home_work') {

            $studentExam = (new StudentExamService($request))->getHomeworkResult();

        } else if ($request->type == 'month-wise-exam-result') {

            $studentExam = (new StudentExamService($request))->getMonthWiseExamResult();

        } else if ($request->type == 'month-wise-homework-result') {
              // here is new api for homework results 
            $studentExam = (new StudentExamService($request))->getMonthWiseHomeworkResult();
            
        } else if ($request->type == 'monthly_exam_result') {

            $studentExam = (new StudentExamService($request))->getMonthlyExamResult();

        } else if ($request->type == 'monthly_home_work_result') {

            $studentExam = (new StudentExamService($request))->getMonthlyHomeWorkResult();

        } 
        else if ($request->type == 'super-admin-homework-result' || $request->type == 'super-admin-exam-result') {
            $studentExam = (new StudentExamService($request))->getSuperAdminHwExamResults();


            
        }else {

            $studentExam = (new StudentExamService($request))->getExamResult();

        }

        return response()->json($studentExam);

    }



    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {

        var_dump('$request->all()');

        exit;

    }



    /**

     * Store a newly created resource in storage.

     *

     * @param \Illuminate\Http\Request $request

     * @return \Illuminate\Http\JsonResponse

     */

    public function store(Request $request)

    {

//        return $request->all();





        $get_answers = $request->all();

        $student_exam = 0;

        $parent_exam = '';

        $isSubmitted = false;

        $examResultSubmit = false;



        if (isset($get_answers[0]['done'])) {

            $exam_id = $get_answers[0]['done']['exam_id'];

            $student_id = $get_answers[0]['done']['student_id'];

            $checkExamPreviousSubmit = $this->checkExamPreviousSubmit($exam_id, $student_id);



            if ($checkExamPreviousSubmit == 1) {

                $examResultSubmit = true;

            }



        }

        if ($examResultSubmit == 1) {

            return response()->json(['msg' => 'You have already submitted this exam.', 'status' => 208]);



        }



        foreach ($get_answers as $key => $answere) {



            if (isset($answere['submit'])) {



                $getExam = Exam::where('id', $answere['submit']['exam_id'])->get();

                $parent_exam = $getExam;

                $sumOfQuestionScore = Question::where('exam_id', $getExam[0]->id)->sum('score');

                $after_exam_submit = StudentExam::where('exam_id', $answere['submit']['exam_id'])->where('student_id', $answere['submit']['student_id'])

                    ->update([

                        'is_submit' => 1,

                        'max_score' => $sumOfQuestionScore



                    ]);



                $student_exam = StudentExam::where('exam_id', $answere['submit']['exam_id'])->where('student_id', $answere['submit']['student_id'])

                    ->get();



                $isSubmitted = true;



                continue;

            }

            $iscorrect = 0;



            if (!empty($answere['question_id'])) {



                if (!empty($answere['answer_id'])) {

                    if (is_array($answere['answer_id'])) {

                        $studentExamQuCheckExist = StudentAnswer::where('student_exam_question_id', $answere['question_id'])->get();

                        foreach ($answere['answer_id'] as $key => $answer_id) {

                            $studentIndAnswer = StudentExamQuestionsAnswer::find($answer_id);

                            if (!empty($answere['matching_id'][$key])) {

                                $studentIndAnswer_matching = StudentExamQuestionsAnswer::find($answere['matching_id'][$key]);

                            }



                            if (count($studentExamQuCheckExist) > 0) {

                                foreach ($studentExamQuCheckExist as $item) {

                                    $studentExamAnsCheckExist = StudentExamQuestionsAnswer::where('student_exam_question_id', $answere['question_id'])->where('id', $answer_id)->first();

                                    $studentExam = StudentAnswer::where('student_exam_question_id', $answere['question_id'])->where('answer_id', $answer_id)->first();

                                    $answer = $studentExamAnsCheckExist->title;

                                    if (!empty($studentExam)) {

                                        $studentAnswer = StudentAnswer::findOrFail($studentExam->id);;

                                        $iscorrect = (int)$studentExamAnsCheckExist->is_correct;

                                        $score = $studentExamAnsCheckExist->score;;

                                    } else {

                                        $studentAnswer = new StudentAnswer();

                                    }

                                    $studentAnswer->student_exam_question_id = $answere['question_id'];

                                    $studentAnswer->answer_id = $answer_id;



                                    if (!empty($answere['matching_id'][$key])) {

                                        $studentAnswer->matching_answer_id = $answere['matching_id'][$key];

                                        $answer = $studentIndAnswer_matching->is_correct;

                                        if ($answer_id == $answere['matching_id'][$key]) {

                                            $score = $studentIndAnswer->score;

                                            $iscorrect = 1;

                                        } else {

                                            $score = 0;

                                            $iscorrect = 0;

                                        }

                                    } else {

                                        if (!empty($studentIndAnswer->matching_answer_id)) {

                                            $answer = $studentIndAnswer_matching->title;

                                        } else {

                                            if ($answere['question_type'] == 'multiple_choice') {

                                                if (isset($answer)) {

                                                    $answer = $answer;

                                                } else {

                                                    $answer = 'N/A';

                                                }



                                            } else {

                                                $answer = 'N/A';

                                            }

                                        }

                                        $studentAnswer->matching_answer_id = null;

                                    }



                                    if (empty($answere['matching_id'][$key])) {

                                        if (!empty($answere['answer'][$key])) {

                                            $answer = $answere['answer'][$key];

                                            $studentIndAnswer = StudentExamQuestionsAnswer::find($answer_id);

                                            if ($studentIndAnswer->is_correct == $answere['answer'][$key]) {

                                                $iscorrect = 1;

                                                $score = $studentIndAnswer->score;

                                            }



                                            if ($answere['question_type'] != 'dropdown_question_type') {

                                                $answer = $studentIndAnswer->title;

                                            }

                                        }

                                    }



                                    if (empty($answere['matching_id'][$key])) {

                                        if ($answere['question_type'] != 'dropdown_question_type') {

                                            if ($answere['question_type'] != 'multiple_choice') {

                                                $iscorrect = is_int($studentIndAnswer->is_correct) ? $studentIndAnswer->is_correct : 0;

                                                $score = $studentIndAnswer->score;;

                                            }



                                        }

                                    }



                                    $studentAnswer->answer = $answer;

                                    $studentAnswer->is_correct = isset($iscorrect) ? $iscorrect : 0;



                                    if (is_int($iscorrect)) {

                                        $studentAnswer->score = isset($score) ? $score : 0;

                                    } else {

                                        $studentAnswer->score = 0;

                                    }

                                    $studentAnswer->save();

                                }

                            } else {

                                $studentAnswer = new StudentAnswer();

                                $studentAnswer->student_exam_question_id = $answere['question_id'];

                                $studentAnswer->answer_id = $answer_id;

                                if (!empty($answere['matching_id'][$key])) {

                                    $studentAnswer->matching_answer_id = $answere['matching_id'][$key];

                                    if ($answer_id == $answere['matching_id'][$key]) {

                                        $score = $studentIndAnswer->score;

                                        $iscorrect = 1;

                                    } else {

                                        $score = 0;

                                        $iscorrect = 0;

                                    }

                                    $answer = $studentIndAnswer_matching->is_correct;

                                } else {

                                    $answer = $studentIndAnswer->title;

                                    if ($studentIndAnswer->is_correct == 1) {

                                        $iscorrect = 1;

                                        $score = $studentIndAnswer->score;

                                    } else {



                                        $iscorrect = is_int($studentAnswer->is_correct) ? $studentAnswer->is_correct : 0;

                                        $score = $studentAnswer->score;;

                                    }



                                }

                                if (!empty($answere['answer'][$key])) {

                                    $answer = $answere['answer'][$key];

                                    if ($studentIndAnswer->is_correct == $answere['answer'][$key]) {

                                        $iscorrect = 1;

                                        $score = $studentIndAnswer->score;

                                    } else {

                                        $iscorrect = 0;

                                        $score = 0;

                                    }

                                }

                                $studentAnswer->answer = $answer;

                                $studentAnswer->is_correct = is_int($iscorrect) ? $iscorrect : 0;

                                if (is_int($iscorrect)) {

                                    $studentAnswer->score = isset($score) ? $score : 0;

                                } else {

                                    $studentAnswer->score = 0;

                                }

                                $studentAnswer->save();

                            }

                            $res = StudentAnswer::where('student_exam_question_id', $answere['question_id'])->whereNotIn('answer_id', $answere['answer_id'])->delete();

                        }

                    } else {

                        $studentExamQuCheckExist = StudentAnswer::where('student_exam_question_id', $answere['question_id'])->first();

                        if (!empty($studentExamQuCheckExist)) {

                            $studentAnswer = StudentAnswer::findOrFail($studentExamQuCheckExist->id);;

                        } else {

                            $studentAnswer = new StudentAnswer();

                        }

                        $studentAnswer->student_exam_question_id = $answere['question_id'];

                        $studentAnswer->answer_id = $answere['answer_id'];



                        $studentIndAnswer = StudentExamQuestionsAnswer::find($answere['answer_id']);

                        $studentAnswer->answer = isset($studentIndAnswer->title) ? $studentIndAnswer->title : '';





                        if ($studentIndAnswer->is_correct == 1) {

                            $iscorrect = 1;

                            $score = $studentIndAnswer->score;

                        } else {

                            $iscorrect = 0;

                            $score = 0;

                        }

                        $studentAnswer->is_correct = is_int($iscorrect) ? $iscorrect : 0;

                        if (is_int($iscorrect)) {

                            $studentAnswer->score = isset($score) ? $score : 0;

                        } else {

                            $studentAnswer->score = 0;

                        }





                        $studentAnswer->save();

                    }



                } else {

                    StudentAnswer::where('student_exam_question_id', $answere['question_id'])->delete();

                }



            }



        }

        if ($isSubmitted) {

            $this->callAfterSubmit($parent_exam, $student_exam);

            return response()->json(['msg' => 'Answer Save Please wait. Do not click any button.'], 200);



        }



        return response()->json(['msg' => 'Answer Save successfully.'], 200);



    }





    public function checkExamPreviousSubmit($exam_id, $student_id)

    {



        $student_exam = StudentExam::where('exam_id', $exam_id)->where('student_id', $student_id)

            ->orderBy('id', 'desc')->first();



        if ($student_exam->is_submit == 1 && $student_exam->duration != 0) {

            return 1;

        } else {

            return 0;

        }





    }





    /**

     * call function after exam submit

     */



    public function callAfterSubmit($parent_exam, $student_exam)

    {



        $exam_id = $parent_exam[0]->id;

        $student_exam_id = $student_exam[0]->id;

        $for_exam_student_exm_id = StudentExam::where('exam_id', $exam_id)->where('student_id', auth()->user()->id)->orderBy('id', 'desc')->first();

        if ($parent_exam[0]->duration_minutes > 0) {

            $this->examAfterSubmit($exam_id, $for_exam_student_exm_id->id);

        }

        if ($parent_exam[0]->retake_time == 0) {



             $this->homeWorkSubmit($exam_id, $student_exam_id);

        }

    }



    public function totalPartsScore($student_exam_id)

    {

        $total_score = 0;

        $parts_score = StudentExamQuestions::with('exam')->where('student_exam_id', $student_exam_id)->whereNull('parent_id')->get();

        foreach ($parts_score as $key => $item) {

            $score_count = 0;

            $studentExamQuestions = StudentExamQuestions::with('studentAnswers')->where('parent_id', $item->id)->get();

            $parts_score[$key]->out_of = $studentExamQuestions->sum("question_score");

            if ($studentExamQuestions) {

                foreach ($studentExamQuestions as $item_answer) {

                    $score_count += StudentAnswer::where('student_exam_question_id', $item_answer->id)->where('is_correct', 1)->get()->sum("score");

                }

            }

            $total_score += $score_count;

        }



        return round($total_score);



    }





    public function homeWorkSubmit($exam_id, $student_exam_id)

    {

        $total_score = $this->totalPartsScore($student_exam_id);



        StudentExam::find($student_exam_id)->update([

            'score' => $total_score

        ]);



        if ($total_score >= 70) {

            $block_Exam = Examlocked::where('student_id', auth()->user()->id)->where('exam_id', $exam_id)->update([

                'is_block' => 0,

            ]);

            StudentExam::find($student_exam_id)->update([

                'status' => 1

            ]);



        } else {

            $answer_id = StudentExamQuestions::where('student_exam_id', $student_exam_id)->pluck('id');

            $delete = StudentAnswer::whereIn('student_exam_question_id', $answer_id)->delete();

        }

    }



    public function examAfterSubmit($exam_id, $student_exam_id)

    {

        $total_score = $this->totalPartsScore($student_exam_id);



        $exam_item = StudentExam::find($student_exam_id);

        $createdDate = Carbon::createFromFormat('Y-m-d H:i:s', $exam_item->created_at)->format('d-m-Y H:i:s');

        $updateDate = Carbon::createFromFormat('Y-m-d H:i:s', $exam_item->updated_at)->format('d-m-Y H:i:s');

        $datediff = strtotime($updateDate) - strtotime($createdDate);

        $minutes = floor($datediff / 60);



        StudentExam::find($student_exam_id)->update([

            'score' => $total_score,

            'spend_time' => $minutes . " min",

        ]);

        if ($total_score >= 70) {

            StudentExam::find($student_exam_id)->update([

                'status' => 1,

            ]);

            $studentExam = (new StudentExamService())->sendCert($student_exam_id);

        }



    }





    public function studentAnswer(Request $request)

    {

        var_dump($request->all());

        exit;

    }



    /**

     * Display the specified resource.

     *

     * @param int $id

     * @return \Illuminate\Http\Response

     */

    public function show($id)

    {





        $parts_score = StudentExamQuestions::with('exam')->where('student_exam_id', $id)->whereNull('parent_id')->get();

        foreach ($parts_score as $key => $item) {

            $score_count = 0;

            $parts_score[$key]->out_of = StudentExamQuestions::where('parent_id', $item->id)->get()->sum("question_score");

            $student_exam_answer = StudentExamQuestions::where('parent_id', $item->id)->get();

            if ($student_exam_answer) {

                foreach ($student_exam_answer as $item_answer) {

                    $score_count += StudentAnswer::where('student_exam_question_id', $item_answer->id)->where('is_correct', 1)->get()->sum("score");

                }

            }

            $parts_score[$key]->points = $score_count;

            $getChild = StudentExamQuestions::with('answers', 'studentAnswers')->where('parent_id', $item->id)->orderBy('sort_id', 'ASC')->get();

            if (!empty($getChild)) {

                foreach ($getChild as $key_ans => $question) {

                    if (isset($question->studentAnswers)) {

                        foreach ($question->studentAnswers as $key22 => $stuanswer) {

                            $question->studentAnswers[$key22]->original_answer = $original = StudentExamQuestionsAnswer::where('student_exam_question_id', $question->id)->where('is_correct', 1)->get();

                        }

                    }

                    foreach ($question->answers as $key2 => $stuanswer) {

                        $get_student_answer = StudentAnswer::where('answer_id', $stuanswer->id)->where('student_exam_question_id', $question->id)->first();

                        // return $original=StudentExamQuestionsAnswer::where('student_exam_question_id',$question->id)->where('is_correct',1)->get();

                        $question->answers[$key2]->substudent_answer = $get_student_answer;

                    }

                }

                $parts_score[$key]->children = $getChild;

            } else {

                $parts_score[$key]->children = array();



            }



        }

        return $parts_score;

    }



    /**

     * Show the form for editing the specified resource.

     *

     * @param int $id

     * @return \Illuminate\Http\Response

     */

    public function edit($id)

    {

        //

    }



    /**

     * Update the specified resource in storage.

     *

     * @param \Illuminate\Http\Request $request

     * @param int $id

     * @return \Illuminate\Http\Response

     */

    public function update(Request $request, $id)

    {

        //

    }



    /**

     * Remove the specified resource from storage.

     *

     * @param int $id

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)

    {

        //

    }



    public function getDailyExamByCompany(Request $request)

    {



        $exam_results = StudentExam::where('is_submit', 1)->where('duration', '>', 0)->whereDate('created_at', Carbon::today())->whereDate('updated_at', Carbon::today())->whereHas('student', function ($student) {

            $student->where('company_id', auth()->user()->company->id);

        })->whereBetween('score', array($request->from_score, $request->to_score))->with(['student', 'exam.course'])->orderBy('id', 'DESC')->paginate(20);

        return response()->json($exam_results);

    }



    public function examLockAfterExam(Request $request)

    {



        $exam = StudentExam::where('exam_id', $request->exam_id)->where('duration', '>', 0)->get();



        if (count($exam)) {

            Examlocked::where('exam_id', $request->exam_id)->where('student_id', auth()->user()->id)->update([

                'is_block' => 0,

            ]);

            return response()->json(['msg' => 'Exam lock successfully.'], 200);



        }





    }



    public function examStatus()

    {

        // Total exam staus

        $totalExamTaken = StudentExam::where('is_submit', 1)->count();

        $totalExamTakenThisWeek = StudentExam::where('is_submit', 1)->whereBetween('updated_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();

        $totalExamTakenYestarday = StudentExam::where('is_submit', 1)->whereDate('updated_at', Carbon::yesterday())->count();

        $totalExamTakenThisMonth = StudentExam::where('is_submit', 1)->whereBetween('updated_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->count();

        $totalExamTakenLastMonth = StudentExam::where('is_submit', 1)->whereBetween('updated_at', [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()])->count();

        $totalExamTakenThisYear = StudentExam::where('is_submit', 1)->whereBetween('updated_at', [Carbon::now()->startOfYear(), Carbon::now()])->count();

        $totalExamTakenLastYear = StudentExam::where('is_submit', 1)->whereYear('updated_at', Carbon::now()->subYear(1)->format('Y'))->count();

        // Total Essays Status

        $totalEssays = EssayAnswer::count();

        $totalEssayLastMonth = EssayAnswer::whereBetween('created_at', [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()])->count();

        $totalEssaysLastYear = EssayAnswer::whereYear('created_at', Carbon::now()->subYear(1)->format('Y'))->count();

        $totalEssaysYestarday = EssayAnswer::whereDate('created_at', Carbon::yesterday())->count();
        

        //Total Speakings

        $totalSpeaking = SpeakingAnswer::count();

        $totalSpeakingLastMonth = SpeakingAnswer::whereBetween('created_at', [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()])->count();

        $totalSpeakingLastYear = SpeakingAnswer::whereYear('created_at', Carbon::now()->subYear(1)->format('Y'))->count();

        $totalSpeakingYestarday = SpeakingAnswer::whereDate('created_at', Carbon::yesterday())->count();



        // Total Assessment

        $totalAssessment = Assessment::count();

        $totalAssessmentLastMonth = Assessment::whereBetween('created_at', [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()])->count();

        $totalAssessmentLastYear = Assessment::whereYear('created_at', Carbon::now()->subYear(1)->format('Y'))->count();

        $totalAssessmentYestarday = Assessment::whereDate('created_at', Carbon::yesterday())->count();

        return response()->json([

            'totalExamTaken' => $totalExamTaken,

            'totalExamTakenYestarday' => $totalExamTakenYestarday,

            'totalExamTakenThisweek' => $totalExamTakenThisWeek,

            'totalExamTakenThisMonth' => $totalExamTakenThisMonth,

            'totalExamTakenLastMonth' => $totalExamTakenLastMonth,

            'totalExamTakenThisYear' => $totalExamTakenThisYear,

            'totalExamTakenLastYear' => $totalExamTakenLastYear,

            'totalEssays' => $totalEssays,

            'totalEssayLastMonth' => $totalEssayLastMonth,

            'totalEssaysLastYear' => $totalEssaysLastYear,

            'totalEssaysYesterday' => $totalEssaysYestarday,



            'totalSpeaking' => $totalSpeaking,

            'totalSpeakingLastMonth' => $totalSpeakingLastMonth,

            'totalSpeakingLastYear' => $totalSpeakingLastYear,

            'totalSpeakingYesterday' => $totalSpeakingYestarday,




            'totalAssessment' => $totalAssessment,

            'totalAssessmentLastMonth' => $totalAssessmentLastMonth,

            'totalAssessmentLastYear' => $totalAssessmentLastYear,

            'totalAssessmentYesterday' => $totalAssessmentYestarday

        ]);



    }



    public function partScore(Request $request)

    {



       

        $query = StudentExamQuestions::with('childrenQuestions')

            ->where('student_exam_id', $request->id)

            ->whereNull('parent_id')

            ->select('id', 'title')->get();

        return StudentExamQuestionsPartResult::collection($query);

      

    }

}

