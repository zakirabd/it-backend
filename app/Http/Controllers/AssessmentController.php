<?php



namespace App\Http\Controllers;



use App\Assessment;

use App\Attendance;

use App\Http\Requests\AssessmentRequest;

use App\Mail\AssessmentCreated;

use App\OneSignalUser;

use App\User;

use Carbon\Carbon;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Http;

use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\Notification;

use Illuminate\Support\Facades\DB;

use GuzzleHttp\Client;

use App\TeacherEnroll;



class AssessmentController extends Controller

{

    /**

     * Display a listing of the resource.

     *

     * @param \Illuminate\Http\Request $request

     * @return \Illuminate\Http\JsonResponse

     */

    public function index(Request $request)

    {



        $assessments = Assessment::with('user', 'staff')->where('user_id', $request->user_id)

            ->where(function ($q) use ($request) {

                $q->where('note', 'like', "%{$request->keyword}%");

            })->orderBy('id', 'DESC');

        if (auth()->user()->role == 'teacher' || auth()->user()->role == 'head_teacher') {

            $assessments->where('staff_id', auth()->user()->id);

        }



        return response()->json($assessments->paginate(20));

    }



    /**

     * Store a newly created resource in storage.

     *

     * @param \Illuminate\Http\Request $request

     * @return \Illuminate\Http\JsonResponse

     */

    public function store(AssessmentRequest $request)

    {

        $mail = collect();

        $assessment = Assessment::create([

            'note' => $request->note,

            'date' => $request->date,

            'home_work' => isset($request->homework) ? $request->homework : 0,

            'participation' => isset($request->participation) ? $request->participation : 0,

            'performance' => isset($request->performance) ? $request->performance : 0,

            'reading_comprehension' => isset($request->readingComprehension) ? $request->readingComprehension : 0,

            'speaking_fluency' => isset($request->speakingFluency) ? $request->speakingFluency : 0,

            'writing_skills' => isset($request->writingSkills) ? $request->writingSkills : 0,

            'listening_skills' => isset($request->listening_skills) ? $request->listening_skills : 0,

            'user_id' => $request->user_id,

            'staff_id' => auth()->user()->id,

        ]);



        $user = User::findOrFail($request->user_id);



        $teacher = User::findOrFail(auth()->user()->id);



        foreach ($user->parent as $item) {

            $mail->push($item);

        }

        // send mails to parent

        Mail::to($mail)->send(new AssessmentCreated($assessment, $user, $teacher));



        // send mails to students

        if ($user->send_email_status == 1) {



            Mail::to($user->email)->send(new AssessmentCreated($assessment, $user, $teacher));

        }

        // send data one signal



        $Student = User::find($request->user_id);

        $student_parent = User::with('parent')->find($request->user_id)->parent->pluck('id');



        $player_id = OneSignalUser::whereIn('user_id', $student_parent)->pluck('player_id');

        try {

            $url = "https://onesignal.com/api/v1/notifications";

            $post = [

                'include_player_ids' => $player_id,

                'app_id' => '4a6748e2-52be-4558-b323-6a6bd7673a63',

                'contents' => (object)[

                    'en' => $Student->full_name . ' Assessment',

                ],

                'headers' => (object)[

                    'en' => $Student->full_name,

                ],

            ];



            Http::withHeaders([

                'Authorization' => 'Basic ZjU1MzAyNjQtOTA1YS00N2UzLTg0NDgtYmY5MDFlZTgzYmQz',

                'Content-Type' => 'application/json'

            ])->post($url, $post);



            return response()->json(['msg' => 'Assessment created successfully.']);



        } catch (\Exception $e) {



            return response()->json($e->getMessage(), 400);

        }



    }



    /**

     * Display the specified resource.

     *

     * @param int $id

     * @return \Illuminate\Http\JsonResponse

     */

    public function show($id)

    {

        return response()->json(Assessment::findOrFail($id));

    }



    /**

     * Update the specified resource in storage.

     *

     * @param \Illuminate\Http\Request $request

     * @param int $id

     * @return \Illuminate\Http\JsonResponse

     */

    public function update(AssessmentRequest $request, $id)

    {



        Assessment::where('id', $id)->update([

            'note' => $request->note,

            'home_work' => $request->homework,

            'participation' => $request->participation,

            'reading_comprehension' => $request->readingComprehension,

            'speaking_fluency' => $request->speakingFluency,

            'writing_skills' => $request->writingSkills,

            'user_id' => $request->user_id,

            'staff_id' => auth()->user()->id,



        ]);



        return response()->json(['msg' => 'Assessment updated successfully.']);

    }



    /**

     * Remove the specified resource from storage.

     *

     * @param int $id

     * @return \Illuminate\Http\JsonResponse

     */

    public function destroy($id)

    {

        Assessment::findOrFail($id)->delete();



        return response()->json(['msg' => 'Assessment deleted successfully.']);

    }



    public function getAssessmentbyStudent(Request $request)

    {



        if ($request->keyword != '') {

            $assessments = Assessment::with('user', 'staff')->where('user_id', $request->student_id)

                ->where(function ($q) use ($request) {

                    $q->where('note', 'like', "%{$request->keyword}%");

                })->orderBy('id', 'DESC')->paginate(10);



        } else {

            $assessments = Assessment::with('user', 'staff')->where('user_id', $request->student_id)->orderBy('id', 'DESC')->

            paginate(10);

        }



        return response()->json($assessments);



    }



    public function companyWiseAssessment(Request $request)

    {



        $startdate = $request->startdate;

        $enddate = $request->enddate;

        $get_company_students = User::where('role', 'student')->where('company_id', auth()->user()->company_id)->pluck('id');

        return $assessment = Assessment::with('user', 'staff')

            ->whereIn('user_id', $get_company_students)

            ->orderBy('id', 'DESC')

            ->whereBetween('created_at', [$startdate . " 00:00:00", $enddate . " 23:59:59"])

            ->get();



    }



    public function assessmentUngraded(Request $request)

    {



        $startdate = $request->startdate;

        $enddate = $request->enddate;

        $get_company_students = DB::table('users')->where('company_id', auth()->user()->company_id)->pluck('id');

        if(auth()->user()->role == 'teacher'){
            $teacher_students = TeacherEnroll::where('teacher_id', auth()->user()->id)->pluck('student_id');
            $get_company_students = DB::table('users')->where('company_id', auth()->user()->company_id)->whereIn('id', $teacher_students)->pluck('id');
        }



        // $get_attendence = DB::table('attendances')

        //     ->whereIn('user_id', $get_company_students)

        //     ->whereBetween('date', [$startdate, $enddate])

        //     ->orderBy('date', 'DESC')

        //     ->Join('users as teacher', 'teacher.id', '=', 'attendances.teacher_id')

        //     ->Join('users as student', 'student.id', '=', 'attendances.user_id')

        //     ->select('teacher.first_name as teacher_name',

        //         'student.first_name as student_name',

        //         'date', 'user_id',

        //         'teacher_id',

        //         DB::raw("CONCAT(student.first_name, ' ' , student.last_name) AS student_name"),

        //         DB::raw("CONCAT(teacher.first_name, ' ' , teacher.last_name) AS teacher_name"),

        //     )

        //     ->get();

         if(auth()->user()->role == 'teacher'){
            $get_attendence = DB::table('attendances')



            ->whereIn('user_id', $get_company_students)

            ->where('teacher_id', auth()->user()->id)

            ->whereBetween('date', [$startdate, $enddate])



            ->orderBy('date', 'DESC')



            ->Join('users as teacher', 'teacher.id', '=', 'attendances.teacher_id')



            ->Join('users as student', 'student.id', '=', 'attendances.user_id')



            ->select('teacher.first_name as teacher_name',



                'student.first_name as student_name',



                'date', 'user_id',



                'teacher_id',



                DB::raw("CONCAT(student.first_name, ' ' , student.last_name) AS student_name"),



                DB::raw("CONCAT(teacher.first_name, ' ' , teacher.last_name) AS teacher_name"),



            )



            ->get();
        }else{
            $get_attendence = DB::table('attendances')



            ->whereIn('user_id', $get_company_students)


            ->whereBetween('date', [$startdate, $enddate])



            ->orderBy('date', 'DESC')



            ->Join('users as teacher', 'teacher.id', '=', 'attendances.teacher_id')



            ->Join('users as student', 'student.id', '=', 'attendances.user_id')



            ->select('teacher.first_name as teacher_name',



                'student.first_name as student_name',



                'date', 'user_id',



                'teacher_id',



                DB::raw("CONCAT(student.first_name, ' ' , student.last_name) AS student_name"),



                DB::raw("CONCAT(teacher.first_name, ' ' , teacher.last_name) AS teacher_name"),



            )



            ->get();
        }



        $ungraded = array();

        foreach ($get_attendence as $key => $attendence) {

            $assessment_ungraded = Assessment::where('user_id', $attendence->user_id)

                ->where('staff_id', $attendence->teacher_id)

                ->whereDate('date', $attendence->date)

                ->get();

            if (count($assessment_ungraded) == 0) {

                $ungraded[] = $attendence;

            }

        }

        return response()->json($ungraded);





    }

}

