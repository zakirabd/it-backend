<?php



namespace App\Services;



use App\Assessment;

use App\Attendance;

use App\Company;

use App\EssayAnswer;

use App\Expenditure;

use App\SpeakingAnswer;

use App\StudentExam;

use App\TeacherEnroll;

use App\User;

use App\Exam;

use App\Course;

use Carbon\Carbon;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Str;

class CompanyService

{



    private $request;

    private $company_student;


    private $teacher_student;

    private $teacher_student_lesson_mode;



    public function __construct($request = null)

    {

        $this->request = $request;

        $this->company_student = User::UnlockStudent(auth()->user()->company_id)->pluck('id');

        

        $this->teacher_student = TeacherEnroll::where('teacher_id', auth()->user()->id)->where('status', '1')->pluck('student_id');

        $this->teacher_student_lesson_mode = TeacherEnroll::where('teacher_id', auth()->user()->id)->where('status', '1')->pluck('lesson_mode');

    }
  //  //////////////////////////////////////////////////////////////////////////////////////////////

    // //////////////GET ALL COMPANY TEACHER STATISTICS FOR COMPANY HEAD OR OFFICE MANAGER

    public function getCompanyAllTeacherStatistics(){

        $date      = Carbon::parse($this->request->date);
        $teacherArray = [];
        $teacherStatistics = [];

        $teachers = User::whereIn('role', ['head_teacher', 'teacher', 'speaking_teacher'])->where(function ($query) {

          

                $query->where('company_id', auth()->user()->company_id);

            

        })->get();



        foreach ($teachers as $teacher) {

            

            $lockStatus = User::with('teacherLock')->findOrFail($teacher->id);

            array_push($teacherArray,  $lockStatus);

        }

        foreach($teacherArray as $item){
            $teacher_statistics = [];
            $teacher_students = TeacherEnroll::where('teacher_id', $item->id)->where('status', '1')->pluck('student_id');

            if($item->teacher_lock == null || $item->teacher_lock && $item->teacher_lock->lock_status == 0){
                $teacher_statistics['staff'] =  $item;
                // teacher name
                $teacher_statistics['teacher_name'] =  $item->full_name;

                // student count

                $teacher_statistics['studentCount'] = TeacherEnroll::where('teacher_id', $item->id)->where('status', '1')->count();

                // attendace

                $teacher_statistics['attendance'] = Attendance::whereIn('user_id', $teacher_students)

                ->where('teacher_id', $item->id)

                ->whereMonth('date', $date->format('m'))->whereYear('date', $date->format('Y'))



                ->count();

                // essay count

                $essay_count = [];

                $teacher_student_essays = EssayAnswer::whereIn('user_id', $teacher_students)



                ->whereMonth('submit_date', $date->format('m'))->whereYear('submit_date', $date->format('Y'))

                ->where('is_closed', '=', 1 )
                ->where('grade', '!=', null )->get();

                foreach($teacher_student_essays as $essay){
                    $essay->essay = DB::table('essays')->where('id', '=', $essay->essay_id)->get();
                    $essay->course_title_essay = DB::table('courses')->where('id', '=', $essay->essay[0]->course_id)->get()[0]->title;
                    $essay->student_lesson_mode = TeacherEnroll::where('teacher_id', $item->id)->where('status', '1')->where('student_id', $essay->user_id)->get();

                    foreach($essay->student_lesson_mode as $items){
                        if(Str::contains($items->lesson_mode, 'English') && Str::contains($essay->course_title_essay, 'English')){
                            array_push($essay_count, $items);
                        }
                    }
                }

                $teacher_statistics['essay'] = count($essay_count);

                // speaking counting

                $speaking_count = [];
                $teacher_student_speakings = SpeakingAnswer::whereIn('user_id', $teacher_students)



                    ->whereMonth('updated_at', $date->format('m'))->whereYear('updated_at', $date->format('Y'))

                    ->where('is_closed', '=', 1 )
                    ->where('grade', '!=', null )->get();

                    foreach($teacher_student_speakings as $speaking){

                        $speaking->speaking = DB::table('speakings')->where('id', '=', $speaking->speaking_id)->get();

                        $speaking->course_title_speaking = DB::table('courses')->where('id', '=', $speaking->speaking[0]->course_id)->get()[0]->title;

                        $speaking->student_lesson_mode = TeacherEnroll::where('teacher_id', $item->id)->where('status', '1')->where('student_id', $speaking->user_id)->get();

                        foreach($speaking->student_lesson_mode as $items){
                            if(Str::contains($items->lesson_mode, 'English') && Str::contains($speaking->course_title_speaking, 'English')){
                                array_push($speaking_count, $items);
                            }
                        }
                    }

                    $teacher_statistics['speaking'] = count($speaking_count);

                    // assessment

                     $teacher_statistics['assessment'] = Assessment::whereIn('user_id', $teacher_students)



                    ->whereMonth('date', $date->format('m'))->whereYear('date', $date->format('Y'))



                    ->count();
    // ////////////////////////////////// home works
                $general_hw = [];

                $SAT_hw = [];
            

                $homeWorksForAll = StudentExam::where('is_submit', 1)


                    ->whereIn('student_id', $teacher_students)   /// muelimin butun telebelerinin imtahnlari gelir



                    ->whereMonth('updated_at', $date->format('m'))->whereYear('updated_at', $date->format('Y'))



                    ->where('duration', '=', 0)


                    ->where('start_time', '=', 0)

                    

                    ->where('score', '>=', 70)->get();

                    foreach($homeWorksForAll as $hw){
                        // array_push($hw_results, Exam::where('id', '=', $hw->exam_id)->get());
                        $hw->exam = Exam::where('id', '=', $hw->exam_id)->get();    //// telebenin verdiyi imtahan 
                        foreach($hw->exam as $items){
                            $hw->course_title = DB::table('courses')->where('id', '=', $items->course_id)->get()[0]->title;
                        }
                        $hw->student_lesson_mode = TeacherEnroll::where('teacher_id', $item->id)->where('status', '1')->where('student_id', $hw->student_id)->get();

                        foreach($hw->student_lesson_mode as $items){
                            if(Str::contains($items->lesson_mode, 'English') && Str::contains($hw->course_title, 'English')){
                                array_push($general_hw, $items);
                            }else if(Str::contains($items->lesson_mode, 'SAT') && Str::contains($hw->course_title, 'SAT')){
                                array_push($SAT_hw, $items);
                            }
                        }
                        
                    }


                
                    $teacher_statistics['monthly_sat_homework_taken'] = count($SAT_hw);

                    $teacher_statistics['monthly_general_english_homework_taken'] = count($general_hw);

                    // /////////////////////////////////// exam results count
                    $general_exams = [];

                    $SAT_exams = [];

                    $examsForAll = StudentExam::where('is_submit', 1)


                        ->whereIn('student_id', $teacher_students)



                        ->whereMonth('updated_at', $date->format('m'))->whereYear('updated_at', $date->format('Y'))



                        ->where('duration', '>', 0)



                        ->where('score', '>=', 70)->get();

                        foreach($examsForAll as $exam){

                            // array_push($exam_results, Exam::where('id', '=', $exam->exam_id)->get());

                            $exam->exam = Exam::where('id', '=', $exam->exam_id)->get();    //// telebenin verdiyi imtahan 
                            foreach($exam->exam as $items){
                                $exam->course_title = DB::table('courses')->where('id', '=', $items->course_id)->get()[0]->title;
                            }
                            $exam->student_lesson_mode = TeacherEnroll::where('teacher_id', $item->id)->where('status', '1')->where('student_id', $exam->student_id)->get();

                            foreach($exam->student_lesson_mode as $items){
                                if(Str::contains($items->lesson_mode, 'English') && Str::contains($exam->course_title, 'English')){
                                    array_push($general_exams, $items);
                                }else if(Str::contains($items->lesson_mode, 'SAT') && Str::contains($exam->course_title, 'SAT')){
                                    array_push($SAT_exams, $items);
                                }
                            }
                        }
                        
                        
                        $teacher_statistics['monthly_general_english_exams_taken'] = count($general_exams);

                        $teacher_statistics['monthly_sat_exam_takens'] = count($SAT_exams);


                        // general english student count

                        $teacher_statistics['general_english_students'] =



                        TeacherEnroll::whereIn('student_id', $teacher_students)->where('teacher_id', $item->id)->where('lesson_mode', 'General English')



                            ->groupBy('student_id')



                            ->pluck('student_id')->count();

                // sat student count

                        $teacher_statistics['sat_students']   =



                        TeacherEnroll::whereIn('student_id', $teacher_students)->where('teacher_id', $item->id)->where('lesson_mode', 'SAT')



                            ->groupBy('student_id')



                            ->pluck('student_id')->count();


            }

            array_push($teacherStatistics, $teacher_statistics);

        }
        return $teacherStatistics;
    }


    /**

     * Filter by staff role

     *

     * @param $request

     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator

     */
     public function getTeacherCompanyStatus(){


        $date      = Carbon::parse($this->request->date);



        $company   = [];
        

        // all students of teacher count

        $company['studentCount'] = TeacherEnroll::where('teacher_id', auth()->user()->id)->where('status', '1')->count();

// //////////////////////////////////////////////////////////////////
        // check in count


        $company['attendance'] = Attendance::whereIn('user_id', $this->teacher_student)


            ->where('teacher_id', auth()->user()->id)
            ->whereMonth('date', $date->format('m'))->whereYear('date', $date->format('Y'))



            ->count();


// ///////////////////////////////////////////////////////////////////////////////////////

// /////////////////// essays count//////////////////////////////

        //  $company['essay']      = EssayAnswer::whereIn('user_id', $this->teacher_student)



        //     ->whereMonth('submit_date', $date->format('m'))->whereYear('submit_date', $date->format('Y'))

        //     ->where('is_closed', '=', 1 )
        //     ->where('grade', '!=', null )

        //     ->count();

        // new code

        $essay_count = [];

        $teacher_student_essays = EssayAnswer::whereIn('user_id', $this->teacher_student)



        ->whereMonth('submit_date', $date->format('m'))->whereYear('submit_date', $date->format('Y'))

        ->where('is_closed', '=', 1 )
        ->where('grade', '!=', null )->get();

        foreach($teacher_student_essays as $essay){
            $essay->essay = DB::table('essays')->where('id', '=', $essay->essay_id)->get();
            $essay->course_title_essay = DB::table('courses')->where('id', '=', $essay->essay[0]->course_id)->get()[0]->title;
            $essay->student_lesson_mode = TeacherEnroll::where('teacher_id', auth()->user()->id)->where('status', '1')->where('student_id', $essay->user_id)->get();

            foreach($essay->student_lesson_mode as $item){
                    if(Str::contains($item->lesson_mode, 'English') && Str::contains($essay->course_title_essay, 'English')){
                    array_push($essay_count, $item);
                }
            }
        }

        $company['essay'] = count($essay_count);


// //////////////////////////////////////////////////////////////////////////////////////////////
// /////////////////////////// SPEAKING COUNT ///////////////////////////////


        // $company['speaking'] = SpeakingAnswer::whereIn('user_id', $this->teacher_student)



        //     ->whereMonth('updated_at', $date->format('m'))->whereYear('updated_at', $date->format('Y'))

        //     ->where('is_closed', '=', 1 )
        //      ->where('grade', '!=', null )
        //     ->count();

        // new line

        $speaking_count = [];
        $teacher_student_speakings = SpeakingAnswer::whereIn('user_id', $this->teacher_student)



            ->whereMonth('updated_at', $date->format('m'))->whereYear('updated_at', $date->format('Y'))

            ->where('is_closed', '=', 1 )
             ->where('grade', '!=', null )->get();

             foreach($teacher_student_speakings as $speaking){

                $speaking->speaking = DB::table('speakings')->where('id', '=', $speaking->speaking_id)->get();

                $speaking->course_title_speaking = DB::table('courses')->where('id', '=', $speaking->speaking[0]->course_id)->get()[0]->title;

                $speaking->student_lesson_mode = TeacherEnroll::where('teacher_id', auth()->user()->id)->where('status', '1')->where('student_id', $speaking->user_id)->get();

                foreach($speaking->student_lesson_mode as $item){
                     if(Str::contains($item->lesson_mode, 'English') && Str::contains($speaking->course_title_speaking, 'English')){
                        array_push($speaking_count, $item);
                    }
                }
            }

            $company['speaking'] = count($speaking_count);

// ///////////////////////////////////////////////////////////////////////////////////////////////////////
// ////////////////////////////////////// REPORTS COUNTS

        $company['assessment'] = Assessment::whereIn('user_id', $this->teacher_student)



            ->whereMonth('date', $date->format('m'))->whereYear('date', $date->format('Y'))



            ->count();

// /////////////////////////////////////////////////////////////////////////////////////
// HOME WORKS COUNT 

        // $hw_results = [];
        // $home_works = [];

        // $general_hw = [];

        // $SAT_hw = [];

        // $homeWorksForAll = StudentExam::where('is_submit', 1)


        //     ->whereIn('student_id', $this->teacher_student)



        //     ->whereMonth('updated_at', $date->format('m'))->whereYear('updated_at', $date->format('Y'))



        //     ->where('duration', '=', 0)


        //     ->where('start_time', '=', 0)

            

        //     ->where('score', '>=', 70)->get();

        //     foreach($homeWorksForAll as $hw){
        //         array_push($hw_results, Exam::where('id', '=', $hw->exam_id)->get());
        //     }
            
        //     foreach($hw_results as $result){
        //         if(count($result) != 0){
        //              $query = DB::table('courses')->where('id', '=', $result[0]->course_id)->get();
        //             array_push($home_works, $query[0]->title);
        //         }
               
        //     }

        //     foreach($home_works as $title){
        //         if(Str::contains($title, 'SAT')){
        //             array_push($SAT_hw, $title);
        //         }else if(Str::contains($title, 'English')){
        //             array_push($general_hw, $title);
        //         }
        //     }
        //     $company['monthly_sat_homework_taken'] = count($SAT_hw);

        //     $company['monthly_general_english_homework_taken'] = count($general_hw);


        // new
        $hw_results = [];
        $home_works = [];

        $general_hw = [];

        $SAT_hw = [];
      

        $homeWorksForAll = StudentExam::where('is_submit', 1)


            ->whereIn('student_id', $this->teacher_student)   /// muelimin butun telebelerinin imtahnlari gelir



            ->whereMonth('updated_at', $date->format('m'))->whereYear('updated_at', $date->format('Y'))



            ->where('duration', '=', 0)


            ->where('start_time', '=', 0)

            

            ->where('score', '>=', 70)->get();

            foreach($homeWorksForAll as $hw){
                // array_push($hw_results, Exam::where('id', '=', $hw->exam_id)->get());
                $hw->exam = Exam::where('id', '=', $hw->exam_id)->get();    //// telebenin verdiyi imtahan 
                foreach($hw->exam as $item){
                    $hw->course_title = DB::table('courses')->where('id', '=', $item->course_id)->get()[0]->title;
                }
                $hw->student_lesson_mode = TeacherEnroll::where('teacher_id', auth()->user()->id)->where('status', '1')->where('student_id', $hw->student_id)->get();

                foreach($hw->student_lesson_mode as $item){
                    if(Str::contains($item->lesson_mode, 'English') && Str::contains($hw->course_title, 'English')){
                        array_push($general_hw, $item);
                    }else if(Str::contains($item->lesson_mode, 'SAT') && Str::contains($hw->course_title, 'SAT')){
                        array_push($SAT_hw, $item);
                    }
                }
                
            }


           
            $company['monthly_sat_homework_taken'] = count($SAT_hw);

            $company['monthly_general_english_homework_taken'] = count($general_hw);


            // ///////////////////////////////////////////////////////////////////////////////////
            // //////////////////////exam count
            // $exam_results = [];
            // $teacher_exams = [];

            // $general_exams = [];

            // $SAT_exams = [];

            // $examsForAll = StudentExam::where('is_submit', 1)


            //     ->whereIn('student_id', $this->teacher_student)



            //     ->whereMonth('updated_at', $date->format('m'))->whereYear('updated_at', $date->format('Y'))



            //     ->where('duration', '>', 0)



            //     ->where('score', '>=', 70)->get();

            //     foreach($examsForAll as $exam){
            //         array_push($exam_results, Exam::where('id', '=', $exam->exam_id)->get());
            //     }
                
            //     foreach($exam_results as $result){
            //         if(count($result) != 0){
            //             $query = DB::table('courses')->where('id', '=', $result[0]->course_id)->get();
            //             array_push($teacher_exams, $query[0]->title); 
            //         }
                    
            //     }

            //     foreach($teacher_exams as $title){
            //         if(Str::contains($title, 'SAT')){
            //             array_push($SAT_exams, $title);
            //         }else if(Str::contains($title, 'English')){
            //             array_push($general_exams, $title);
            //         }
            //     }
            //     $company['monthly_general_english_exams_taken'] = count($general_exams);

            //     $company['monthly_sat_exam_takens'] = count($SAT_exams);

            // new

            $exam_results = [];
            $teacher_exams = [];

            $general_exams = [];

            $SAT_exams = [];

            $examsForAll = StudentExam::where('is_submit', 1)


                ->whereIn('student_id', $this->teacher_student)



                ->whereMonth('updated_at', $date->format('m'))->whereYear('updated_at', $date->format('Y'))



                ->where('duration', '>', 0)



                ->where('score', '>=', 70)->get();

                foreach($examsForAll as $exam){

                    // array_push($exam_results, Exam::where('id', '=', $exam->exam_id)->get());

                    $exam->exam = Exam::where('id', '=', $exam->exam_id)->get();    //// telebenin verdiyi imtahan 
                    foreach($exam->exam as $item){
                        $exam->course_title = DB::table('courses')->where('id', '=', $item->course_id)->get()[0]->title;
                    }
                    $exam->student_lesson_mode = TeacherEnroll::where('teacher_id', auth()->user()->id)->where('status', '1')->where('student_id', $exam->student_id)->get();

                    foreach($exam->student_lesson_mode as $item){
                        if(Str::contains($item->lesson_mode, 'English') && Str::contains($exam->course_title, 'English')){
                            array_push($general_exams, $item);
                        }else if(Str::contains($item->lesson_mode, 'SAT') && Str::contains($exam->course_title, 'SAT')){
                            array_push($SAT_exams, $item);
                        }
                    }
                }
                
                
                $company['monthly_general_english_exams_taken'] = count($general_exams);

                $company['monthly_sat_exam_takens'] = count($SAT_exams);

                // /////////////////////////////////////////////////////////////////////////////////
               ///////////////// teacher general english students count//////////////////////
                
                $company['general_english_students'] =



                    TeacherEnroll::whereIn('student_id', $this->teacher_student)->where('teacher_id', auth()->user()->id)->where('lesson_mode', 'General English')



                        ->groupBy('student_id')



                        ->pluck('student_id')->count();



                $company['sat_students']   =



                    TeacherEnroll::whereIn('student_id', $this->teacher_student)->where('teacher_id', auth()->user()->id)->where('lesson_mode', 'SAT')



                        ->groupBy('student_id')



                        ->pluck('student_id')->count();





        return $company;

    }

    public function getCompanyStatus()

    {



        $date      = Carbon::parse($this->request->date);

        $company   = [];



        // $company['studentCount'] = $this->company_student->count();
        $company_teachers = User::whereIn('role', ['teacher', 'head_teacher'])->where('company_id', auth()->user()->company_id)->pluck('id');
        $now = Carbon::now();
        $old_students = Attendance::whereIn('teacher_id', $company_teachers)
        ->whereMonth('created_at', $date->format('m'))->whereYear('created_at', $date->format('Y'))
        ->select('user_id')->distinct()->get();

      

// /////new condition

        // $company['studentCount'] = $this->company_student->count();
        
        if($date->month == $now->month && $date->year == $now->year ){
            $company['studentCount'] = $this->company_student->count();
        }else{
            $company['studentCount'] = count($old_students);
        }



        // $company['monthly_homework_taken'] = StudentExam::where('is_submit', 1)

        //     ->whereIn('student_id', $this->company_student)

        //     ->whereMonth('updated_at', $date->format('m'))->whereYear('updated_at', $date->format('Y'))

        //     ->where('duration', '=', 0)

        //     ->where('start_time', '=', 0)
            
        //     ->where('score', '>=', 70)
            

        //     ->count();


////////////////////////////////////////////////////////////////////// HW
// $company['monthly_homework_taken']
        $results = [];
        $home_works = [];

        $homeWorksForAll = StudentExam::where('is_submit', 1)



            ->whereIn('student_id', $this->company_student)



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
            
             $company['monthly_homework_taken'] = $home_works;
            


/////////////////////////////////////////////////////////////////////////////////////////////


        $company['attendance'] = Attendance::whereIn('user_id', User::where('company_id', auth()->user()->company_id)->pluck('id'))

            ->whereMonth('date', $date->format('m'))->whereYear('date', $date->format('Y'))

            ->count();



        $company['essay']      = EssayAnswer::whereIn('user_id', $this->company_student)

            ->whereMonth('submit_date', $date->format('m'))->whereYear('submit_date', $date->format('Y'))
            ->where('is_closed', '=', 1 )
            ->where('grade', '!=', null )

            ->count();



        $company['speaking'] = SpeakingAnswer::whereIn('user_id', $this->company_student)

            ->whereMonth('updated_at', $date->format('m'))->whereYear('updated_at', $date->format('Y'))
            ->where('is_closed', '=', 1 )
             ->where('grade', '!=', null )

            ->count();



        $company['assessment'] = Assessment::whereIn('user_id', $this->company_student)

            ->whereMonth('date', $date->format('m'))->whereYear('date', $date->format('Y'))

            ->count();



        return $company;



    }



    public function dailyCompanyStatus()

    {



        $companyid = auth()->user()->company_id;



        $companies = array();



        $companies['studentCount'] = User::UnlockStudent($companyid)->count();



        $companies['exam_taken'] = StudentExam::where('is_submit', 1)

            ->whereIn('student_id', User::where('company_id', $companyid)->pluck('id'))

            ->where('duration', '>', 0)

            ->where('score', '>=', 70)

            ->whereDate('created_at', Carbon::today())->whereDate('updated_at', Carbon::today())

            ->count();



        $companies['attendance'] = Attendance::whereIn('user_id', User::where('company_id', $companyid)->pluck('id'))

            ->whereDate('created_at', Carbon::today())->whereDate('updated_at', Carbon::today())

            ->count();



        $companies['essay'] = EssayAnswer::whereIn('user_id', User::where('company_id', $companyid)->pluck('id'))

            ->whereDate('submit_date', Carbon::today())->whereDate('submit_date', Carbon::today())
            ->where('is_closed', '=', 1 )
            ->where('grade', '!=', null )

            ->count();



        $companies['speaking'] = SpeakingAnswer::whereIn('user_id', User::where('company_id', $companyid)->pluck('id'))

            ->whereDate('created_at', Carbon::today())->whereDate('updated_at', Carbon::today())
             ->where('is_closed', '=', 1 )
            ->where('grade', '!=', null )

            ->count();



        $companies['assessment'] = Assessment::whereIn('user_id', User::where('company_id', $companyid)->pluck('id'))

            ->whereDate('created_at', Carbon::today())->whereDate('updated_at', Carbon::today())

            ->count();



        // $companies['homework_taken'] = StudentExam::where('is_submit', 1)

        //     ->whereIn('student_id', User::where('company_id', $companyid)->pluck('id'))

        //     ->whereDate('created_at', Carbon::today())->whereDate('updated_at', Carbon::today())

        //     ->where('duration', '=', 0)

        //     ->where('start_time', '=', 0)


        //     ->count();

        $results = [];
        $home_works = [];
        // $companies['homework_taken']

        $homeWorksForAll = StudentExam::where('is_submit', 1)



            ->whereIn('student_id', User::where('company_id', $companyid)->pluck('id'))



            ->whereDate('created_at', Carbon::today())->whereDate('updated_at', Carbon::today())



            ->where('duration', '=', 0)



            ->where('start_time', '=', 0)

            // new
            ->where('score', '>=', 70)->get();


            foreach($homeWorksForAll as $hw){
                array_push($results, Exam::where('id', '=', $hw->exam_id)->get());
            }

            foreach($results as $result){
                $query = DB::table('courses')->where('id', '=', $result[0]->course_id)->get();
                 array_push($home_works, $query[0]->title);
            }
            
             $companies['homework_taken'] = $home_works;

        return $companies;

    }



    public function ungradedByTeachers()

    {

        $date = Carbon::parse($this->request->date);



        $ungraded_by_teachers     = array();

        $essay_ungraded_data      = array();

        $assessment_ungraded_data = array();

        $get_company_students     =

            User::where('role', 'student')->where('company_id', auth()->user()->company_id)->pluck('id');

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





        return $ungraded_by_teachers;



    }



    /**

     * @return \Illuminate\Http\JsonResponse

     */

    public function getExamTaken()

    {

        $date = Carbon::parse($this->request->date);



        $general_english_student =

            TeacherEnroll::whereIn('student_id', $this->company_student)->where('lesson_mode', 'General English')

                ->groupBy('student_id')

                ->pluck('student_id');

        $sat_student             =

            TeacherEnroll::whereIn('student_id', $this->company_student)->where('lesson_mode', 'SAT')

                ->groupBy('student_id')

                ->pluck('student_id');



        $general_english_exam_submit = $this->getExamMonthly($general_english_student, $date);

        $sat_exam_submit             = $this->getExamMonthly($sat_student, $date);



        return response()->json([

            'general_english_student'      => $general_english_student->count(),

            'sat_student'                  => $sat_student->count(),

            'general_english_exam'         => $general_english_exam_submit,

            'sat_exam_submit'              => $sat_exam_submit,

        ]);



    }



    /**

     * get submitted  exam monthly passed and failed.

     * @param $student_id

     * @param $date

     * @return mixed

     */

    public function getExamMonthly($student_id, $date)

    {

        return StudentExam::where('is_submit', 1)

            ->whereIn('student_id', $student_id)

            ->where('duration', '>', 0)

            ->where('is_submit', '=', 1)

            ->whereMonth('updated_at', $date->format('m'))

            ->whereYear('updated_at', $date->format('Y'))

            ->count();



    }



    /**

     * @return \Illuminate\Http\JsonResponse

     */

    public function getStudentEnrollment()

    {

        // $users =

        //     User::where('role', 'student')->where('company_id', auth()->user()->company_id)->select('id', 'created_at')

        //         ->whereYear('created_at', '=', $this->request->year)

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
        //     User::where('role', 'student')->where('company_id', auth()->user()->company_id)->select('id', 'manual_lock_status', 'created_at')
        //         ->whereYear('created_at', '=', $this->request->year)
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
        // $allStatistics = ['all_students' => $userArr, 'locked_students' => $locked_userArr];
        // return response()->json($allStatistics);

        $trials = TeacherEnroll::whereIn('student_id', User::where('company_id', auth()->user()->company_id)->pluck('id'))->where('trial_mode', '1')
        ->whereYear('created_at', '=', $this->request->year)
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

        $enrolled_students = TeacherEnroll::whereIn('student_id', $this->company_student)->where('lesson_mode','!=', 'Trial class')->where('trial_mode','1')
        ->whereYear('created_at', '=', $this->request->year)
        ->get()
        ->groupBy(function ($value) {

            return Carbon::parse($value->created_at)->format('m');

        });

        $enrolled_user_count = [];

        $enrolled_user_arr    = [];



        foreach ($enrolled_students as $key => $value) {

            $enrolled_user_count[(int)$key] = count($value);

        }



        for ($month = 1; $month <= 12; $month++) {

            if (!empty($enrolled_user_count[$month])) {

                $enrolled_user_arr[$month] = $enrolled_user_count[$month];

            } else {

                $enrolled_user_arr[$month] = 0;

            }

        }

        $general_enrolled_students = User::whereIn('id', $this->company_student)
        ->whereYear('created_at', '=', $this->request->year)
        ->get()
        ->groupBy(function ($value) {

            return Carbon::parse($value->created_at)->format('m');

        });

        $general_enrolled_user_count = [];

        $general_enrolled_user_arr    = [];



        foreach ($general_enrolled_students as $key => $value) {

            $general_enrolled_user_count[(int)$key] = count($value);

        }



        for ($month = 1; $month <= 12; $month++) {

            if (!empty($general_enrolled_user_count[$month])) {

                $general_enrolled_user_arr[$month] = $general_enrolled_user_count[$month];

            } else {

                $general_enrolled_user_arr[$month] = 0;

            }

        }

        $allStatistics = ['all_students' => $userArr, 'enrolled_students' => $enrolled_user_arr, 'general_enrolled_students' => $general_enrolled_user_arr];

         return response()->json($allStatistics);

        // return response()->json($userArr);

    }



    /**

     * get all company status for chief auditor role

     * @return \Illuminate\Http\JsonResponse

     */

    public function allCompanyStatus()

    {



        $companies = Company::select('id', 'name')->get();



        foreach ($companies as $company) {



            $company->daily_students_registered_count   = User::where('role', 'student')

                ->where('company_id', $company->id)

                ->whereDate('created_at', Carbon::today())

                ->count();

            $company->weekly_students_registered_count  = User::where('role', 'student')

                ->where('company_id', $company->id)

                ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])

                ->count();

            $company->monthly_students_registered_count = User::where('role', 'student')

                ->where('company_id', $company->id)

                ->whereMonth('created_at', Carbon::now()->month)

                ->count();

            $company->yearly_students_registered_count  = User::where('role', 'student')

                ->where('company_id', $company->id)

                ->whereYear('created_at', Carbon::now()->year)

                ->count();



            $company->daily_teachers_registered_count   = User::where(function ($q) {

                $q->where('role', 'teacher')->orWhere('role', 'head_teacher')->orWhere('role', 'speaking_teacher');

            })->where('company_id', $company->id)

                ->whereDate('created_at', Carbon::today())

                ->count();

            $company->weekly_teachers_registered_count  = User::where(function ($q) {

                $q->where('role', 'teacher')->orWhere('role', 'head_teacher')->orWhere('role', 'speaking_teacher');

            })->where('company_id', $company->id)

                ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])

                ->count();

            $company->monthly_teachers_registered_count = User::where(function ($q) {

                $q->where('role', 'teacher')->orWhere('role', 'head_teacher')->orWhere('role', 'speaking_teacher');

            })->where('company_id', $company->id)

                ->whereMonth('created_at', Carbon::now()->month)

                ->count();

            $company->yearly_teachers_registered_count  = User::where(function ($q) {

                $q->where('role', 'teacher')->orWhere('role', 'head_teacher')->orWhere('role', 'speaking_teacher');

            })->where('company_id', $company->id)

                ->whereYear('created_at', Carbon::now()->year)

                ->count();



            //exam taken by companies

            $company->totalExamDaily    =

                StudentExam::where('is_submit', 1)->where('duration', '>', 0)->whereIn('student_id', User::where('company_id', $company->id)->pluck('id'))

                    ->whereDate('created_at', Carbon::today())

                    ->count();

            $company->weekly_exam_taken =

                StudentExam::where('is_submit', 1)->where('duration', '>', 0)->whereIn('student_id', User::where('company_id', $company->id)->pluck('id'))

                    ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])

                    ->count();



            $company->monthly_exam_taken =

                StudentExam::where('is_submit', 1)->where('duration', '>', 0)->whereIn('student_id', User::where('company_id', $company->id)->pluck('id'))

                    ->whereMonth('created_at', Carbon::now()->month)

                    ->count();



            $company->yearly_exam_taken =

                StudentExam::where('is_submit', 1)->where('duration', '>', 0)->whereIn('student_id', User::where('company_id', $company->id)->pluck('id'))

                    ->whereYear('created_at', Carbon::now()->year)

                    ->count();



            //for chief editor dashboard payment calculation

            $company->weekly_income   = Attendance::where('company_id', $company->id)

                ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('amount');

            $company->weekly_spending = Expenditure::where('company_id', $company->id)

                ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('amount');

        }



        return response()->json($companies);

    }

}

