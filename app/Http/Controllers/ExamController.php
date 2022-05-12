<?php



namespace App\Http\Controllers;



use App\Answer;

use App\Exam;

use App\Examlocked;

use App\Helpers\UploadHelper;

use App\Http\Requests\ExamRequest;

use App\Question;

use App\Services\ExamService;

use App\StudentExam;

use App\StudentExamQuestions;

use App\StudentExamQuestionsAnswer;

use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;



class ExamController extends Controller

{

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\JsonResponse

     */

    public function index(Request $request)

    {



        if ($request->query('user_type') && $request->query('user_type') == 'student_lock') {

            $exams = (new ExamService($request))->getLockUnlockExams();

        } else if ($request->query('user_type') && $request->query('user_type') == 'student') {

            $exams = (new ExamService($request))->getStudentExams();

        } else if ($request->query('home_work') && $request->query('home_work') == 1) {

            $exams = (new ExamService($request))->getlockUnlockHomeWork();

        } else {

            $exams = (new ExamService($request))->getExams();

        }



        return response()->json(['status' => 200, 'data' => $exams, 'timeZone' => config('app.timezone')]);

    }



    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {

        //

    }



    /**

     * @param Request $request

     * @param $id

     * @return \Illuminate\Http\JsonResponse|void

     */

    public function examLockUnlock(Request $request, $id)

    {

        $examlocked = Examlocked::with('exam')->findOrFail($id);



        $homeWork = $examlocked->exam->retake_minutes;



        // for home work and exam process

        if ($homeWork == 0) {

            $examlocked->update([

                'is_block' => $request->is_block,

            ]);

        } else {

            if(request('calling_manual') == 'start'){
                $examlocked->update([
                    'is_block'     => $request->is_block,
                    'retake_count' => $request->retake_count,
                    'timer_start'  => now(),
                ]);

                if($examlocked->is_block == 1 && $examlocked->timer_start == null){
                    $examlocked->update([
                        'timer_start'  => now(),
                    ]);
                }
            }else if(request('calling_manual') == 'stop'){
                 $examlocked->update([
                    'timer_start' => null,
                ]);
            }

        }



        $examlocked->save();

        if ($examlocked->is_block == 1) {

             $this->afterExamUnlock($examlocked, $request);

        }



        return response()->json(['msg' => 'Process successfully done.'], 200);

    }



    /**

     * @param ExamRequest $request

     * @return \Illuminate\Http\JsonResponse

     */

    public function store(ExamRequest $request)

    {

        $exam = new Exam();

        $exam->fill($request->all());

        if ($request->hasFile('exam_image')) {

            $exam->exam_image = UploadHelper::imageUpload($request->file('exam_image'), 'avatars');

        }

        $exam->save();

        return response()->json(['msg' => 'Exam created successfully.', 'data' => $exam], 200);

    }



    /**

     * Display the specified resource.

     *

     * @param \App\Exam $exam

     * @return \Illuminate\Http\Response

     */

    public function show($id)

    {

        return response()->json(Exam::findOrFail($id), 200);

    }



    /**

     * Show the form for editing the specified resource.

     *

     * @param \App\Exam $exam

     * @return \Illuminate\Http\Response

     */

    public function edit(Exam $exam)

    {

        //

    }



    /**

     * Update the specified resource in storage.

     *

     * @param \Illuminate\Http\Request $request

     * @param \App\Exam $exam

     * @return \Illuminate\Http\Response

     */

    public function update(ExamRequest $request, $id)

    {

        $exam = Exam::findOrFail($id);

        $exam->fill($request->all());

        $exam->save();

        return response()->json(['msg' => 'Exam Updated successfully.'], 200);

    }



    /**

     * Remove the specified resource from storage.

     *

     * @param \App\Exam $exam

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)

    {

        $exam = Exam::findOrFail($id);

        if ($exam->exam_image) {

            Storage::delete('public/' . $exam->exam_image);

        }

        $exam->delete();

        return response()->json(['msg' => 'Exam has been deleted successfully'], 200);

    }



    /**

     * @param $examlocked

     * @param $request

     */

    public function afterExamUnlock($examlocked, $request)

    {

        $get_exam = Exam::find($examlocked->exam_id);



        if ($get_exam->retake_time == 0 && $get_exam->retake_minutes == 0) {

            StudentExam::where('student_id', $examlocked->student_id)->where('exam_id', $examlocked->exam_id)->whereNull('is_submit')->update([

                'is_submit' => 1

            ]);

        } else {

            StudentExam::where('student_id', $examlocked->student_id)->where('exam_id', $examlocked->exam_id)->whereNull('is_submit')->update([

                'is_submit' => 1

            ]);

        }





         $studentExam = $this->storeStudentExamTable($examlocked, $request->is_homework, $get_exam);



        $get_questions = Question::where('exam_id', $studentExam['exam_id'])->whereNull('parent_id')->get();

        foreach ($get_questions as $key => $item) {

            $studentExamQuestion                       = new StudentExamQuestions();

            $studentExamQuestion->student_exam_id      = $studentExam['id'];

            $studentExamQuestion->question_id          = $item->id;

            $studentExamQuestion->question_description = $item->description;

            $studentExamQuestion->audio_file           = $item->audio_file;

            $studentExamQuestion->question_image       = $item->question_image;

            $studentExamQuestion->video_link           = $item->video_link;

            $studentExamQuestion->video_file           = $item->video_file;

            $studentExamQuestion->title                = $item->title;

            $studentExamQuestion->question_type        = $item->type;

            $studentExamQuestion->question_score       = $item->score;

            $studentExamQuestion->save();

            $get_questions_children = Question::where('exam_id', $studentExam['exam_id'])->where('parent_id', $item->id)->get();

            foreach ($get_questions_children as $key => $itemChildren) {

                $studentExamQuestionchildren                       = new StudentExamQuestions();

                $studentExamQuestionchildren->student_exam_id      = $studentExam['id'];

                $studentExamQuestionchildren->question_id          = $itemChildren->id;

                $studentExamQuestionchildren->parent_id            = $studentExamQuestion->id;

                $studentExamQuestionchildren->sort_id              = $itemChildren->sort_id;

                $studentExamQuestionchildren->title                = $itemChildren->title;

                $studentExamQuestionchildren->question_type        = $itemChildren->type;

                $studentExamQuestionchildren->sub_type             = $itemChildren->sub_type;

                $studentExamQuestionchildren->question_score       = $itemChildren->score;

                $studentExamQuestionchildren->question_description = $itemChildren->question_description;

                $studentExamQuestionchildren->description          = $itemChildren->description;

                $studentExamQuestionchildren->question_image       = $itemChildren->question_image;

                $studentExamQuestionchildren->audio_file           = $itemChildren->audio_file;

                $studentExamQuestionchildren->video_link           = $itemChildren->video_link;

                // $studentExamQuestionchildren->video_file           = $itemChildren->video_file;

                $studentExamQuestionchildren->save();

                $get_questions_answer = Answer::where('question_id', $itemChildren->id)->get();

                if (!empty($get_questions_answer)) {

                    foreach ($get_questions_answer as $item_answer) {

                        $get_questions_atudent_answer                           = new StudentExamQuestionsAnswer();

                        $get_questions_atudent_answer->student_exam_question_id = $studentExamQuestionchildren->id;

                        $get_questions_atudent_answer->title                    = $item_answer->title;

                        $get_questions_atudent_answer->is_correct               = $item_answer->is_correct;

                        $get_questions_atudent_answer->score                    = $item_answer->score;

                        $get_questions_atudent_answer->save();

                    }

                }

            }

        }



    }



    /**

     * @param $examlocked

     * @param $is_homework

     * @param $get_exam

     * @return mixed

     */

    public function storeStudentExamTable($examlocked, $is_homework, $get_exam)

    {

        $time = Carbon::parse($examlocked->updated_at);

        $studentExam = [

            'student_id' => $examlocked->student_id,

            'exam_id'    => $get_exam->id,

            'exam_title' => $get_exam->title,

            'score'      => 0,

            'end_time'   => $time->addMinutes($get_exam->duration_minutes),

        ];

        if ($is_homework == 1) {

            $studentExam['start_time'] = 0;

            $studentExam['duration']   = 0;



        } else {



            $studentExam['start_time'] = $examlocked->updated_at;

            $studentExam['duration']   = $get_exam->duration_minutes;

        }



       return StudentExam::create($studentExam);



    }



}

