<?php



namespace App\Http\Controllers;

use App\Assessment;

use App\Attendance;

use App\Company;

use App\EssayAnswer;

use App\Helpers\UploadHelper;

use App\Http\Requests\CompanyRequest;

use App\Services\CompanyService;

use App\SpeakingAnswer;

use App\StudentExam;

use App\TeacherEnroll;

use App\User;

use Carbon\Carbon;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Storage;

use App\Exam;
use App\Course;
use Illuminate\Support\Str;




class CompanyController extends Controller

{

    /**

     * Display a listing of the resource.

     *

     * @param \Illuminate\Http\Request $request

     * @return \Illuminate\Http\JsonResponse

     */

    public function index(Request $request)

    {



        $companies = Company::with('user')

            ->where(function ($query) use ($request) {

                if ($request->keyword != '') {

                    $query->whereHas('user', function ($q) use ($request) {

                        $q->where(DB::raw("concat(first_name,' ',last_name)"), 'like', "%{$request->keyword}%");

                    })

                        ->orwhere('name', 'like', "%{$request->keyword}%")

                        ->orWhere('description', 'like', "%{$request->keyword}%")

                        ->orWhere('address', 'like', "%{$request->keyword}%")

                        ->orWhere('city', 'like', "%{$request->keyword}%")

                        ->orWhere('country', 'like', "%{$request->keyword}%");

                }

            })

            ->latest('id')

            ->take($request->page*20)->get();



        return response()->json($companies);

    }

        // /////////////////// get all teachers statistics for manager role
    public function companyAllTeacherStatistics(Request $request){
        return (new CompanyService($request))->getCompanyAllTeacherStatistics();
    }


    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\JsonResponse

     */

    public function allCompanies()

    {



        if (\request()->query('type') && \request()->query('type') == 'not_assigned') {

            return response()->json(Company::whereNull('user_id')->get());

        }



        $companies = Company::all();



        return response()->json($companies);

    }



    /**

     * Store a newly created resource in storage.

     *

     * @param \Illuminate\Http\Request $request

     * @return \Illuminate\Http\JsonResponse

     */

    public function store(CompanyRequest $request)

    {

        $company = new Company();

        $company->fill($request->all());



        if ($request->hasFile('company_avatar')) {

            $company->company_avatar = UploadHelper::imageUpload($request->file('company_avatar'), 'avatars');

        }



        $company->save();



        return response()->json(['msg' => 'Company created successfully.']);

    }



    /**

     * Display the specified resource.

     *

     * @param int $id

     * @return \Illuminate\Http\JsonResponse

     */

    public function show($id)

    {

        $company = Company::findOrFail($id);

        $student = User::where('role', 'student')

            ->where('company_id', $company->id)

            ->get();

        // except lock

        $company->studentCount = $student->filter(function ($item) {

            return $item->attendance_lock_status == 0 && $item->manual_lock_status == 0;

        })->values()->count();

        return response()->json($company);

    }



    /**

     * Update the specified resource in storage.

     *

     * @param \Illuminate\Http\Request $requestro

     * @param int $id

     * @return \Illuminate\Http\JsonResponse

     */

    public function update(CompanyRequest $request, $id)

    {

        $company = Company::findOrFail($id);

        $company->fill($request->all());



        if ($request->hasFile('company_avatar')) {

            if ($company->avatar_url) {

                Storage::delete('public/' . $company->company_avatar);

            }



            $company->company_avatar = UploadHelper::imageUpload($request->file('company_avatar'), 'avatars');

        }



        $company->save();



        return response()->json(['msg' => 'Company updated successfully.']);

    }



    /**

     * Remove the specified resource from storage.

     *

     * @param int $id

     * @return \Illuminate\Http\JsonResponse

     */

    public function destroy($id)

    {

        $company = Company::findOrFail($id);



        if ($company->avatar_url) {

            Storage::delete('public/' . $company->company_avatar);

        }



        $company->delete();



        return response()->json(['msg' => 'Company has been deleted successfully.']);

    }



    /**

     * Display a listing of the resource.

     *

     * @param int $id

     * @return \Illuminate\Http\JsonResponse

     */

    public function courses($id)

    {



        return response()->json(Company::findOrFail($id)->courses->map(function ($item) {

            $item->text = isset($item->course) ? $item->course->title : '';

            $item->value = isset($item->course) ? $item->course->id : '';

            return $item;

        }));



    }



    /** get Company status

     * @param Request $request

     * @return \Illuminate\Http\JsonResponse

     */
     //  //////////////////////////////////////////////////////////////////////////////////////////////////////////
    //  GET TEACHER MANAGER STUDENT ENROLLMENT DATA///////////////////////////////////

    // public function teacherManagerStudentEnrollment(Request $request){

    //     $date = $request->date;

    //     $companies = Company::all();
    //     // return $companies;
    //     $teachermanagerStatistics = [];
    //     foreach($companies as $company){

    //         $users =



    //         User::where('role', 'student')->where('company_id', $company->id)->select('id', 'created_at')



    //             ->whereYear('created_at', '=', $date)



    //             ->get()



    //             ->groupBy(function ($value) {



    //                 return Carbon::parse($value->created_at)->format('m');



    //             });







    //     $user_count = [];



    //     $userArr    = [];







    //     foreach ($users as $key => $value) {



    //         $user_count[(int)$key] = count($value);



    //     }







    //     for ($month = 1; $month <= 12; $month++) {



    //         if (!empty($user_count[$month])) {



    //             $userArr[$month] = $user_count[$month];



    //         } else {



    //             $userArr[$month] = 0;



    //         }



    //     }

    //      // ////////////////////////////////////////////////////////////////////////////////////////////

    //     // locked students count

    //     $usersLocked =

    //         User::where('role', 'student')->where('company_id', $company->id)->select('id', 'manual_lock_status', 'created_at')

    //             ->whereYear('created_at', '=', $date)

    //             ->where('manual_lock_status', '=', 1)

    //             ->get()

    //             ->groupBy(function ($value) {

    //                 return Carbon::parse($value->created_at)->format('m');

    //             });



    //     $locked_user_count = [];

    //     $locked_userArr    = [];



    //     foreach ($usersLocked as $key => $value) {

    //         $locked_user_count[(int)$key] = count($value);

    //     }



    //     for ($month = 1; $month <= 12; $month++) {

    //         if (!empty($locked_user_count[$month])) {

    //             $locked_userArr[$month] = $locked_user_count[$month];

    //         } else {

    //             $locked_userArr[$month] = 0;

    //         }

    //     }

    //     $allStatistics = ['all_students' => $userArr, 'locked_students' => $locked_userArr, 'company_name' => $company->name];
    //     array_push($teachermanagerStatistics, $allStatistics);
      

    //     }
    //       return response()->json($teachermanagerStatistics);
    // }

     public function teacherManagerStudentEnrollment(Request $request){

        $date = $request->date;

        $companies = Company::all();
        // return $companies;
        $teachermanagerStatistics = [];
        foreach($companies as $company){

            $company_unlock_students = User::UnlockStudent($company->id)->pluck('id');
            $company_all_students = User::where('company_id', $company->id)->pluck('id');

             // ///////////////////////////////////////////////////////////////////////////////////////////////////////////////


        $trials = TeacherEnroll::whereIn('student_id', $company_all_students)->where('trial_mode', '1')
        ->whereYear('created_at', '=', $date)
        ->get()
        ->groupBy(function ($value) {

            return Carbon::parse($value->created_at)->format('m');

        });

        $user_count = [];



        $userArr    = [];







        foreach ($trials as $key => $value) {



            $user_count[(int)$key] = count($value);



        }







        for ($month = 1; $month <= 12; $month++) {



            if (!empty($user_count[$month])) {



                $userArr[$month] = $user_count[$month];



            } else {



                $userArr[$month] = 0;



            }



        }


        // enrolled students

        $enrolled_students = TeacherEnroll::whereIn('student_id', $company_unlock_students)->where('lesson_mode','!=', 'Trial class')->where('trial_mode','1')
        ->whereYear('created_at', '=', $date)
        ->get()
        ->groupBy(function ($value) {

            return Carbon::parse($value->created_at)->format('m');

        });

        $locked_user_count = [];

        $locked_userArr    = [];



        foreach ($enrolled_students as $key => $value) {

            $locked_user_count[(int)$key] = count($value);

        }



        for ($month = 1; $month <= 12; $month++) {

            if (!empty($locked_user_count[$month])) {

                $locked_userArr[$month] = $locked_user_count[$month];

            } else {

                $locked_userArr[$month] = 0;

            }

        }

        // //////
        $general_enrolled_students = User::whereIn('id', $company_unlock_students)
        ->whereYear('created_at', '=', $date)
        ->get()
        ->groupBy(function ($value) {

            return Carbon::parse($value->created_at)->format('m');

        });

        $general_locked_user_count = [];

        $general_locked_userArr    = [];



        foreach ($general_enrolled_students as $key => $value) {

            $general_locked_user_count[(int)$key] = count($value);

        }



        for ($month = 1; $month <= 12; $month++) {

            if (!empty($general_locked_user_count[$month])) {

                $general_locked_userArr[$month] = $general_locked_user_count[$month];

            } else {

                $general_locked_userArr[$month] = 0;

            }

        }

        $allStatistics = ['all_students' =>  $userArr, 'enrolled_students' => $locked_userArr, 'general_enrolled_students' => $general_locked_userArr, 'company_name' => $company->name];
        array_push($teachermanagerStatistics, $allStatistics);
      


            // 
            // ///////////////////////////////////////////////////////////////////////////////////////
        //     $users =



        //     User::where('role', 'student')->where('company_id', $company->id)->select('id', 'created_at')



        //         ->whereYear('created_at', '=', $date)



        //         ->get()



        //         ->groupBy(function ($value) {



        //             return Carbon::parse($value->created_at)->format('m');



        //         });







        // $user_count = [];



        // $userArr    = [];







        // foreach ($users as $key => $value) {



        //     $user_count[(int)$key] = count($value);



        // }







        // for ($month = 1; $month <= 12; $month++) {



        //     if (!empty($user_count[$month])) {



        //         $userArr[$month] = $user_count[$month];



        //     } else {



        //         $userArr[$month] = 0;



        //     }



        // }

        //  // ////////////////////////////////////////////////////////////////////////////////////////////

        // // locked students count

        // $usersLocked =

        //     User::where('role', 'student')->where('company_id', $company->id)->select('id', 'manual_lock_status', 'created_at')

        //         ->whereYear('created_at', '=', $date)

        //         ->where('manual_lock_status', '=', 1)

        //         ->get()

        //         ->groupBy(function ($value) {

        //             return Carbon::parse($value->created_at)->format('m');

        //         });



        // $locked_user_count = [];

        // $locked_userArr    = [];



        // foreach ($usersLocked as $key => $value) {

        //     $locked_user_count[(int)$key] = count($value);

        // }



        // for ($month = 1; $month <= 12; $month++) {

        //     if (!empty($locked_user_count[$month])) {

        //         $locked_userArr[$month] = $locked_user_count[$month];

        //     } else {

        //         $locked_userArr[$month] = 0;

        //     }

        // }


       

        }
          return response()->json($teachermanagerStatistics);
    }
// ////////////////////////////////////////////////////////////////////////////



    public function companySummary(Request $request)

    {

        // for a single company

        if (auth()->user()->role == 'company_head' || auth()->user()->role == 'office_manager') {

            $companies = (new CompanyService($request))->getCompanyStatus();

            return response()->json([

                'data' => $companies,

            ]);



        }else if(auth()->user()->role == 'teacher' || auth()->user()->role == 'head_teacher' || auth()->user()->role == 'speaking_teacher'){

            $companies = (new CompanyService($request))->getTeacherCompanyStatus();



            return response()->json([



                'data' => $companies,



            ]);
        }



        // all Companies

        $date = Carbon::parse($request->date);
         $now = Carbon::now();


        $companies = Company::all();

        foreach ($companies as $company) {



            // get unlock student

            $company_student = User::UnlockStudent($company->id);

            $company_teachers = User::whereIn('role', ['teacher', 'head_teacher'])->where('company_id', $company->id)->pluck('id');
        
            $old_students = Attendance::whereIn('teacher_id', $company_teachers)
            ->whereMonth('created_at', $date->format('m'))->whereYear('created_at', $date->format('Y'))
            ->select('user_id')->distinct()->get();

            // $company->studentCount = $company_student->count();
            if($date->month == $now->month && $date->year == $now->year ){
                $company->studentCount = $company_student->count();
            }else{
               $company->studentCount = count($old_students);
            }





            $general_english_student = TeacherEnroll::whereIn('student_id', $company_student->pluck('id'))

                ->where('lesson_mode', 'General English')

                ->groupBy('student_id')

                ->pluck('student_id');



            $company->general_english_student = $general_english_student->count();



            $company->general_english_exam_submit = (new CompanyService())->getExamMonthly($general_english_student, $date);



            $sat_student = TeacherEnroll::whereIn('student_id', $company_student->pluck('id'))

                ->where('lesson_mode', 'SAT')

                ->groupBy('student_id')

                ->pluck('student_id');



            $company->sat_student = $sat_student->count();



            $company->sat_student_exam_submit = (new CompanyService())->getExamMonthly($sat_student, $date);



            $company->monthly_exam_taken = StudentExam::where('is_submit', 1)

                ->whereIn('student_id', User::where('company_id', $company->id)->pluck('id'))

                ->where('duration', '>', 0)

                ->where('score', '>=', 70)

                ->whereMonth('updated_at', $date->format('m'))->whereYear('updated_at', $date->format('Y'))

                ->count();


            $results = [];
            $home_works = [];
            $sat_hw = [];
            $gen_hw = [];


            // $company->monthly_homework_taken = StudentExam::where('is_submit', 1)

            //     ->whereIn('student_id', User::where('company_id', $company->id)->pluck('id'))

            //     ->whereMonth('updated_at', $date->format('m'))->whereYear('updated_at', $date->format('Y'))

            //     ->where('duration', '=', 0)

            //     ->where('start_time', '=', 0)
                
            //     ->where('score', '>=', 70)

            //     ->count();

            $homeWorksForAll = StudentExam::where('is_submit', 1)



                ->whereIn('student_id', User::where('company_id', $company->id)->pluck('id'))



                ->whereMonth('updated_at', $date->format('m'))->whereYear('updated_at', $date->format('Y'))



                ->where('duration', '=', 0)



                ->where('start_time', '=', 0)

                

                ->where('score', '>=', 70)->get();



                foreach($homeWorksForAll as $hw){
                array_push($results, Exam::where('id', '=', $hw->exam_id)->get());
                }

                foreach($results as $result){
                    $query = DB::table('courses')->where('id', '=', $result[0]->course_id)->get();
                    array_push($home_works, $query[0]->title);
                }
                
                foreach($home_works as $title){
                    if(Str::contains($title, 'SAT')){
                        array_push($sat_hw, $title);
                    }else{
                        array_push($gen_hw, $title);
                    }
                }
            
                $company->monthly_sat_homework_taken = count($sat_hw);

                $company->monthly_general_homework_taken = count($gen_hw);



            $company->attendance = Attendance::whereIn('user_id', User::where('company_id', $company->id)->pluck('id'))

                ->whereMonth('date', $date->format('m'))->whereYear('date', $date->format('Y'))

                ->count();

            $company->essay = EssayAnswer::whereIn('user_id', $company_student->pluck('id'))

                ->whereMonth('submit_date', $date->format('m'))->whereYear('submit_date', $date->format('Y'))

                ->where('is_closed', '=', 1 )
                ->where('grade', '!=', null )

                ->count();



            $company->speaking = SpeakingAnswer::whereIn('user_id', $company_student->pluck('id'))

                ->whereMonth('updated_at', $date->format('m'))->whereYear('updated_at', $date->format('Y'))

                ->where('is_closed', '=', 1 )
                ->where('grade', '!=', null )

                ->count();

            $company->speaking_company = SpeakingAnswer::whereIn('user_id', $company_student->pluck('id'))

                ->count();



            $company->speaking_all = SpeakingAnswer::whereIn('user_id', $company_student->pluck('id'))

                ->count();



            $company->assessment = Assessment::whereIn('user_id', $company_student->pluck('id'))

                ->whereMonth('date', $date->format('m'))->whereYear('date', $date->format('Y'))

                ->count();

            $ungraded_by_teachers     = array();
            $essay_ungraded_data      = array();
            $assessment_ungraded_data = array();
            $get_company_students     =
                User::where('role', 'student')->where('company_id', $company->id)->pluck('id');
            // essays ungraded
            $essay_ungraded = EssayAnswer::withCount('teachers')->orderBy('teachers_count', 'desc')->where('grade', null)
                ->where('is_submitted', 1)
                ->where('is_closed', 0)
                ->whereIn('user_id', $get_company_students)
                ->whereMonth('created_at', $date->format('m'))->whereYear('created_at', $date->format('Y'))->get();
            $index          = 0;
            foreach ($essay_ungraded as $key_main => $teacher) {
                foreach ($teacher->teachers as $key => $item) {
                    $teacher->teachers[$key]['teacher_name'] = DB::table('users')->where('id', $item->teacher_id)
                        ->select([
                            'users.first_name', 'users.last_name',
                            DB::raw("CONCAT(users.first_name, '  ', users.last_name) as full_name"),
                        ])->first();
                    $essay_ungraded_data[$index]['teacher']  = $teacher->teachers[$key]->teacher_name->full_name;
                    $index++;
                }
            }
            $ungraded_by_teachers['essay_ungraded'] = count($essay_ungraded_data);
            // assessment ungraded
            $get_attendence = DB::table('attendances')
                ->whereIn('user_id', $get_company_students)
                ->Join('users as teacher', 'teacher.id', '=', 'attendances.teacher_id')
                ->Join('users as student', 'student.id', '=', 'attendances.user_id')
                ->whereMonth('date', $date->format('m'))->whereYear('date', $date->format('Y'))->get();

            foreach ($get_attendence as $key => $attendence) {
                $assessment_ungraded = Assessment::where('user_id', $attendence->user_id)
                    ->where('staff_id', $attendence->teacher_id)
                    ->whereDate('date', $attendence->date)
                    ->get();
                if (count($assessment_ungraded) == 0) {
                    $assessment_ungraded_data[] = $attendence;
                }
            }
            $ungraded_by_teachers['assessment_ungraded_data'] = count($assessment_ungraded_data);

            // speaking ungraded
            $speaking_ungraded_data = array();
            $speakings_ungraded     = SpeakingAnswer::with('speaking', 'user', 'teachers')
                ->whereNull('grade')
                ->withCount('teachers')
                ->orderBy('teachers_count', 'desc')
                ->whereIn('user_id', $get_company_students)
                ->whereMonth('created_at', $date->format('m'))->whereYear('created_at', $date->format('Y'))->get();

            $speaking_ungraded_index = 0;
            foreach ($speakings_ungraded as $key_main => $teacher) {
                foreach ($teacher->teachers as $key => $item) {
                    $teacher->teachers[$key]['teacher_name']                     =
                        DB::table('users')->where('id', $item->teacher_id)
                            ->select([
                                'users.first_name', 'users.last_name',
                                DB::raw("CONCAT(users.first_name, ' ', users.last_name) as full_name"),
                            ])->first();
                    $speaking_ungraded_data[$speaking_ungraded_index]['teacher'] =
                        $teacher->teachers[$key]->teacher_name->full_name;

                    $speaking_ungraded_data[$speaking_ungraded_index]['title'] = $teacher->speaking->title;

                    $speaking_ungraded_data[$speaking_ungraded_index]['student_info'] = $teacher->user->full_name;
                    $speaking_ungraded_index++;
                }
            }
            $ungraded_by_teachers['speaking_ungraded_data'] = count($speaking_ungraded_data);
            $company->ungradeds = $ungraded_by_teachers;

        }

        return response()->json([

            'data'             => $companies,

            'total_student'    => $companies->sum('studentCount'),

            'total_exam'       => $companies->sum('monthly_exam_taken'),

            'total_attendance' => $companies->sum('attendance'),

            'total_essay'      => $companies->sum('essay'),

            'total_assessment' => $companies->sum('assessment'),

            'total_speaking'   => $companies->sum('speaking'),

            'total_homework'   => $companies->sum('monthly_homework_taken'),

        ]);

    }



    /** get daily Company status

     * @param Request $request

     * @return array

     */

    public function dailyCompanyStatus(): array

    {

        return  (new CompanyService())->dailyCompanyStatus();

    }



    /** get Exam taken

     * @param Request $request

     * @return \Illuminate\Http\JsonResponse

     */

    public function getExamTaken(Request $request): \Illuminate\Http\JsonResponse

    {



        return  (new CompanyService($request))->getExamTaken();

    }



    /** get ungraded by teacher

     * @param Request $request

     * @return array

     */

    public function ungradedByTeachers(Request $request): array

    {

        return $data = (new CompanyService($request))->ungradedByTeachers();

    }



    /**

     * @param Request $request

     * @return \Illuminate\Http\JsonResponse

     */

    public function typesOfStudents(): \Illuminate\Http\JsonResponse

    {

        return response()->json([

            'general_english'       => $this->lessonWiseStudent('General English'),

            'sat'                   => $this->lessonWiseStudent('SAT'),

            'ielts'                 => $this->lessonWiseStudent('IELTS'),

            'toefl'                 => $this->lessonWiseStudent('TOEFL'),

            'gre_gmat'              => $this->lessonWiseStudent('GRE-GMAT'),

            //////////////////////////////////////////////////////
            'duolingo'              => $this->lessonWiseStudent('Duolingo'),    //// changed
            'school_math'           => $this->lessonWiseStudent('School Math'),   /// added 
            'university'            => $this->lessonWiseStudent('University'), /// added
            'sat_subject'           => $this->lessonWiseStudent('SAT Subject'),   /// aded
            'intensive_gre_gmat'    => $this->lessonWiseStudent('Intensive GRE, GMAT'), /// added
            'general_kids'          => $this->lessonWiseStudent('General KIDS'),  ///addedd


            'others_english'        => $this->lessonWiseStudent('Other English'),

            'intensive_english'     => $this->lessonWiseStudent('Intensive English'),

            'intensive_sat'         => $this->lessonWiseStudent('Intensive SAT'),

            'intensive_ielts'       => $this->lessonWiseStudent('INTENSIVE IELTS'),

            'intensive_toefl'       => $this->lessonWiseStudent('INTENSIVE TOEFL'),
            

//////////////////////////////////////////////////////
            'trial_class'           => $this->lessonWiseStudent('Trial class'),     //////////  changed

            'high_school'           => $this->lessonWiseStudent('High School'),

            'secondary_school'      => $this->lessonWiseStudent('Secondary School'),
            
            'elementary_school'     => $this->lessonWiseStudent('Elementary School'),

            'speaking_mode'     =>   $this->lessonWiseStudent('Speaking Mode'),

            'it_fundamental'     =>   $this->lessonWiseStudent('IT FUNDAMENTAL'),
            // new new new 02/14/2022


            'es_lang_1_1'  => $this->lessonWiseStudent('ES Lang. 1-1'),
            'es_lang_group'  => $this->lessonWiseStudent('ES Lang. Group'),
            'es_maths_1_1'  => $this->lessonWiseStudent('ES Maths. 1-1'),
            'es_maths_group'  => $this->lessonWiseStudent('ES Maths group'),
            'es_pre_school_1_1'  => $this->lessonWiseStudent('ES Pre-school 1-1'),
            'es_pre_school_group'  => $this->lessonWiseStudent('ES Pre-school Group'),
            'es_school_prep'  => $this->lessonWiseStudent('ES School Prep 1-1'),
            // ES School Prep 1-1
        ]);

    }



    /**

     * @param $lesson_mode

     * @return mixed

     */

    public function lessonWiseStudent($lesson_mode)

    {

        $get_company_students = User::UnlockStudent(auth()->user()->company_id)->pluck('id');

        return TeacherEnroll::whereIn('student_id', $get_company_students)->where('lesson_mode', $lesson_mode)

            ->groupBy('student_id')

            ->get()->count();

    }



    /** get Student enrollment

     * @param Request $request

     * @return \Illuminate\Http\JsonResponse

     */

    public function studentEnrollment(Request $request): \Illuminate\Http\JsonResponse

    {

        return  (new CompanyService($request))->getStudentEnrollment();



    }



    /**

     * @param Request $request

     * @return \Illuminate\Http\JsonResponse

     */

    public function allCompaniesStatus(Request $request): \Illuminate\Http\JsonResponse

    {

        return $data = (new CompanyService($request))->allCompanyStatus();

    }

}

