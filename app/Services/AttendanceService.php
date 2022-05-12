<?php



namespace App\Services;

use App\TeacherBonus;

use App\Attendance;

use App\Events\CalendarEvent;

use App\Events\StudentAttendanceLockCheckEvent;

use App\Events\StudentAttendanceUnlockCheckEvent;

use App\TeacherEnroll;

use App\User;

use Carbon\Carbon;

use Event;

use Illuminate\Support\Facades\DB;

use App\TeacherEnrollsLog;
use App\TeacherEnrollLocks;
use App\StudentGroup;

class AttendanceService

{



    /** display attendance data on calendar.

     * @param $request

     * @return mixed

     */

    public function getAttendance($request)

    {

        // $user  = auth()->user();
        // new new new new new new new
        if(auth()->user()->role == 'parent'){
          $user  = User::findOrFail($request->student_id);  
        }else{
             $user  = auth()->user(); 
        }

        $query = Attendance::whereMonth('date', $request->month)->whereYear('date', $request->year);



        if ($user->role == 'student' || auth()->user()->role == 'parent') {

            $query->where('user_id', $user->id);

        } else {

            $query->where('user_id', $request->user_id)->where('teacher_id', $request->teacher_id);

        }



        return $query->get();



    }
    public function getTeacherSalary($request){

        $date     = Carbon::parse($request->date);

        if($request->teacher_id){
            $teacher_bonus = TeacherBonus::where('teacher_id', $request->teacher_id)->where('date', $request->date)->get();
        }

       


        $bonus_count = 0;
        for($i = 0; $i < count($teacher_bonus); $i++){
            $bonus_count = $bonus_count+ $teacher_bonus[$i]->bonus;
        }

        $enroll_locks_id = TeacherEnrollLocks::where('teacher_id', $request->teacher_id)
                                                
                                                // ->where('date', $request->date)
                                                ->pluck('enroll_id');


        $teacher_students = TeacherEnroll::with('student')->whereNotIn('id', $enroll_locks_id)->where('teacher_id', $request->teacher_id)->get();
        $teacher_lock_students = TeacherEnroll::with('student')->whereIn('id', $enroll_locks_id)->where('teacher_id', $request->teacher_id)->get();
        $final_data = [];

        foreach($teacher_students as $student){
            if($student->student_group_id != '999' && $student->student_group_id != '9999'){
                $student->group_name = StudentGroup::findOrFail($student->student_group_id)->title;
            }else{
                $student->group_name = 'One to One';
            }

            $student->check_in = Attendance::where('teacher_id', $request->teacher_id)
                                ->where('user_id', $student->student_id)
                                ->where('student_group_id', $student->student_group_id)
                                // ->where('status', '1')
                                ->whereMonth('date', $date->format('m'))
                                ->whereYear('date', $date->format('Y'))
                                ->get();
            array_push($final_data, $student);
        }

        foreach($teacher_lock_students as $student){
            if($student->student_group_id != '999' && $student->student_group_id != '9999'){
                $student->group_name = StudentGroup::findOrFail($student->student_group_id)->title. ' Locked';
            }else{
                $student->group_name = 'One to One';
            }

            $student->check_in = Attendance::where('teacher_id', $request->teacher_id)
                                ->where('user_id', $student->student_id)
                                ->where('student_group_id', $student->student_group_id)
                                // ->where('status', '0')
                                ->whereMonth('date', $date->format('m'))
                                ->whereYear('date', $date->format('Y'))
                                ->get();
            $student->lock_date = TeacherEnrollLocks::where('enroll_id', $student->id)->first()->date;
            array_push($final_data, $student);

        }
        //  $bonus_count
        return ['check_ins'=> $final_data, 'bonus'=> $bonus_count];
    }


    /**

     * Paid  status change in attendance table

     * Change student attendance status in user table

     * @param $request

     */

    public function studentAttendanceStatusUnlock($request)

    {

        $attendance = Attendance::where('user_id', $request->student_id)->where('paid', 0)->get();

        foreach ($attendance as $student) {

            Attendance::where('id', $student->id)->update([

                'paid' => 1

            ]);

        }

        User::findOrFail($request->student_id)->update([

            'attendance_lock_status' => 0

        ]);

    }



    /** Store single payment for student in Attendance DB.

     * @param $request

     * @return mixed

     */

    public function singlePayment($request)

    {

        Attendance::findOrFail($request->event_id)->update([

            'amount'     => $request->amount,

            'paid'       => 1,

            'company_id' => $request->company_id

        ]);



        Event::dispatch(new StudentAttendanceUnlockCheckEvent($request->student_id));

    }



    public function attendanceReport($request)

    {

//        return TeacherEnroll::where('student_id',2648)->get();



        $date     = Carbon::parse($request->date);

        if($request->teacher_id){
            $teacher_bonus = TeacherBonus::where('teacher_id', $request->teacher_id)->where('date', $request->date)->get();
        }
        $bonus_count = 0;

        for($i = 0; $i < count($teacher_bonus); $i++){
            $bonus_count = $bonus_count+ $teacher_bonus[$i]->bonus;
        }

        $students = User::with(['attendances' => function ($q) use ($request, $date) {

            $q->whereMonth('date', $date->format('m'))->whereYear('date', $date->format('Y'));

            if ($request->teacher_id) {

                $q->where('teacher_id', $request->teacher_id);

            }

        }])->leftJoin('teacher_enrolls', 'users.id', '=', 'teacher_enrolls.student_id')

            ->leftJoin('student_groups', 'student_groups.id', '=', 'teacher_enrolls.student_group_id')

            ->select('users.*',

                'teacher_enrolls.*',

                'student_groups.title',

                DB::raw(

                    "

                    (CASE

                        WHEN FLOOR( teacher_enrolls.lesson_houre / 60 ) > 1

                            THEN CONCAT(  SEC_TO_TIME(teacher_enrolls.lesson_houre * 60), ' hrs')



                        WHEN FLOOR(teacher_enrolls.lesson_houre/60) = 1

                            THEN CONCAT( SEC_TO_TIME(teacher_enrolls.lesson_houre * 60),' hr')



                        ELSE

                            CONCAT(teacher_enrolls.lesson_houre, ' min')



                    END) AS total_lesson

                    "

                )

            )



            ->withCount(['attendances' => function ($q) use ($request, $date) {

               if(auth()->user()->role == 'company_head' || auth()->user()->role == 'office_manager' || auth()->user()->role == 'teacher_manager' || auth()->user()->role == 'parent'){
                    if($request->query_type && $request->query_type == 'counting'){
                        $q->whereMonth('date', $date->format('m'))->whereYear('date', $date->format('Y'));
                    }else{
                        $q->where('paid', 0);
                    }
                    // $q->where('paid', 0);
                }else{
                    $q->whereMonth('date', $date->format('m'))->whereYear('date', $date->format('Y'));
                }

                if ($request->teacher_id) {

                    $q->where('teacher_id', $request->teacher_id);

                }

            }])->where(function ($q) use ($request) {

                if ($request->teacher_id) {

                    $q->where('teacher_enrolls.teacher_id', $request->teacher_id);

                } elseif ($request->company_id) {

                    $q->where('users.company_id', $request->company_id);

                }

                if ($request->parent_id) {

                    $q->whereHas('parent', function ($query) {

                        $query->where('id', auth()->id());

                    });

                }

            })->where('users.role', 'student')

            ->orderBy('attendances_count', 'desc')->get();


            
// ///////////////////////////////////////////////////////////////////////////////////////////////////////
// /////////////////////////////////////DELETER ENROLLS///////////////////////////////////////


$deleted_students = User::with(['attendances' => function ($q) use ($request, $date) {



            $q->whereMonth('date', $date->format('m'))->whereYear('date', $date->format('Y'));



            if ($request->teacher_id) {



                $q->where('teacher_id', $request->teacher_id);



            }



        }])
        ->leftJoin('teacher_enrolls_logs', 'users.id', '=', 'teacher_enrolls_logs.student_id')



            ->leftJoin('student_groups', 'student_groups.id', '=', 'teacher_enrolls_logs.student_group_id')



            ->select('users.*',



                'teacher_enrolls_logs.*',



                'student_groups.title',



                DB::raw(



                    "



                    (CASE



                        WHEN FLOOR( teacher_enrolls_logs.lesson_houre / 60 ) > 1



                            THEN CONCAT(  SEC_TO_TIME(teacher_enrolls_logs.lesson_houre * 60), ' hrs')







                        WHEN FLOOR(teacher_enrolls_logs.lesson_houre/60) = 1



                            THEN CONCAT( SEC_TO_TIME(teacher_enrolls_logs.lesson_houre * 60),' hr')







                        ELSE



                            CONCAT(teacher_enrolls_logs.lesson_houre, ' min')







                    END) AS total_lesson



                    "



                )



            )







            ->withCount(['attendances' => function ($q) use ($request, $date) {



               if(auth()->user()->role == 'company_head' || auth()->user()->role == 'office_manager' || auth()->user()->role == 'teacher_manager' || auth()->user()->role == 'parent'){
                //    /////////////////////////////////////////////////////////////////////////////////////////////
                // //////////////     ADDED NEW CONDITION ///////////////////////////////////////////
                    if($request->query_type && $request->query_type == 'counting'){
                        $q->whereMonth('date', $date->format('m'))->whereYear('date', $date->format('Y'));
                    }else{
                        $q->where('paid', 0);
                    }
                    // $q->where('paid', 0);

                }else{
                    
                    $q->whereMonth('date', $date->format('m'))->whereYear('date', $date->format('Y'));

                }



                if ($request->teacher_id) {



                    $q->where('teacher_id', $request->teacher_id);



                }



            }])->where(function ($q) use ($request) {



                if ($request->teacher_id) {



                    $q->where('teacher_enrolls_logs.teacher_id', $request->teacher_id);



                } elseif ($request->company_id) {



                    $q->where('users.company_id', $request->company_id);



                }



                if ($request->parent_id) {



                    $q->whereHas('parent', function ($query) {



                        $query->where('id', auth()->id());



                    });



                }



            })->where('users.role', 'student')



            ->orderBy('attendances_count', 'desc')->get();
            // ->whereMonth('teacher_enrolls_logs.created_at','>=', $date->format('m'))->whereYear('teacher_enrolls_logs.created_at','>=', $date->format('Y'))->get();

        // /////////////////new new new new

        // foreach($students as $student){
        //     $student->check_in = Attendance::where('user_id', $student->student_id)->where('teacher_id', $student->teacher_id)->whereMonth('date', $date->format('m'))->whereYear('date', $date->format('Y'))->get();
        // }

        // foreach($deleted_students as $student){
        //     $student->check_in = Attendance::where('user_id', $student->student_id)->where('teacher_id', $student->teacher_id)->whereMonth('date', $date->format('m'))->whereYear('date', $date->format('Y'))->get();
        // }
        foreach($students as $student){
            $student->check_in = Attendance::where('user_id', $student->student_id)->where('teacher_id', $student->teacher_id)->whereMonth('date', $date->format('m'))->whereYear('date', $date->format('Y'))->get();
            $student->check_in_all = Attendance::where('user_id', $student->student_id)->where('teacher_id', $student->teacher_id)->where('lesson_mode', $student->lesson_mode)->whereMonth('date', $date->format('m'))->whereYear('date', $date->format('Y'))->get();
            if(count($student->check_in_all) != 0){
                if(auth()->user()->role == 'company_head' && $request->query_type && $request->query_type == 'counting'){
                    $student->attendances_count = count($student->check_in_all); 
                }else if(auth()->user()->role != 'company_head'){
                    $student->attendances_count = count($student->check_in_all); 
                }
               
            }
            
        }

        foreach($deleted_students as $student){
            $student->check_in = Attendance::where('user_id', $student->student_id)->where('teacher_id', $student->teacher_id)->whereMonth('date', $date->format('m'))->whereYear('date', $date->format('Y'))->get();
            $student->check_in_all = Attendance::where('user_id', $student->student_id)->where('teacher_id', $student->teacher_id)->where('lesson_mode', $student->lesson_mode)->whereMonth('date', $date->format('m'))->whereYear('date', $date->format('Y'))->get();
            if(count($student->check_in_all) != 0){
               if(auth()->user()->role == 'company_head' && $request->query_type && $request->query_type == 'counting'){
                    $student->attendances_count = count($student->check_in_all); 
                }else if(auth()->user()->role != 'company_head'){
                    $student->attendances_count = count($student->check_in_all); 
                }
            }
        }





        if (auth()->user()->role == 'auditor' || auth()->user()->role == 'accountant') {

            // return response()->json(['students' => $students->get(), 'bonus'=>$bonus_count]);
             return response()->json(['students' => $students, 'bonus'=>$bonus_count, 'deleted_students' =>$deleted_students]);

        } else if (auth()->user()->role == 'company_head' || auth()->user()->role == 'office_manager' || auth()->user()->role == 'teacher_manager') {

            return $request->query('page')

                ? response()->json(['students' => $students, 'bonus'=>$bonus_count, 'deleted_students' =>$deleted_students])

                : '';

        } else {

            return $request->query('page')

                ? response()->json($students)

                : '';

        }





    }



    /** Student Class Check in attendance Table.

     * @param $request

     * @return mixed

     */

    public function todayClassCheckIn($request)

    {



        return Attendance::where('user_id',$request->student_id)

            ->where('teacher_id', $request->teacher_id)

            ->where('paid', 0)

            ->where('lesson_mode', $request->lesson_mode)->where('date', Carbon::today())->count();

    }



    public function storeAttendance($request)

    {


        $teacher_enroll = TeacherEnroll::where('student_id', $request->student_id)->where('lesson_mode', $request->lesson_mode)->where('teacher_id', $request->teacher_id)->where('status', '1')->first();

        Attendance::create([

            'event'       => $request->event,

            'user_id'     => $request->student_id,

            'teacher_id'  => $request->teacher_id,

            'lesson_mode' => $request->lesson_mode,

            'date'        => $request->today,

            'lesson_houre' => $teacher_enroll->lesson_houre,

            'study_mode' => $teacher_enroll->study_mode,

            'student_group_id' => $teacher_enroll->student_group_id


        ]);



        $event = \GuzzleHttp\json_decode($request->event);

        event(new CalendarEvent([

            'student_id' => $request->student_id,

            'event'      => $event

        ]));



        Event::dispatch(new StudentAttendanceLockCheckEvent($request->student_id));

        return Attendance::where('user_id', $request->student_id)->where('paid', 0)->where('teacher_id', $request->teacher_id)->count();

    }





    public function storeAttendanceManually($request)

    {

        $teacher_enroll = TeacherEnroll::where('student_id', $request->student_id)->where('lesson_mode', $request->lesson_mode)->where('teacher_id', $request->teacher_id)->where('status', '1')->first();

        Attendance::create([

            'event'        => $request->event,

            'user_id'      => $request->student_id,

            'teacher_id'   => $request->teacher_id,

            'lesson_mode'  => $request->lesson_mode,

            'date'         => $request->today,

            'manually_add' => 1,

            'lesson_houre' => $teacher_enroll->lesson_houre,

            'study_mode' => $teacher_enroll->study_mode,

            'student_group_id' => $teacher_enroll->student_group_id

        ]);



        $event = \GuzzleHttp\json_decode($request->event);

        Event::dispatch(new StudentAttendanceLockCheckEvent($request->student_id));



        event(new CalendarEvent([

            'student_id' => $request->student_id,

            'event'      => $event

        ]));



    }





}

