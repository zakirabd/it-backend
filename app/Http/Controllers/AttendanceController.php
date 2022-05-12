<?php



namespace App\Http\Controllers;

use App\Attendance;

use App\Http\Requests\AttendanceCheckRequest;

use App\Http\Requests\AttendanceManuallyStoreRequest;

use App\Http\Requests\AttendanceReportRequest;

use App\Http\Requests\AttendanceRequest;

use App\Http\Requests\AttendanceSetPaymentRequest;

use App\Http\Requests\SinglePaymentRequest;

use App\Notifications\ParentPaymentNotify;

use App\Notifications\StudentLocked;

use App\Notifications\StudentUnlocked;

use App\PaymentNote;

use App\Services\AttendanceService;

use App\TeacherEnroll;

use App\User;

use Carbon\Carbon;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Notification;

use App\TeacherEnrollLocks;

use Event;



class AttendanceController extends Controller

{

    /** display attendance  data on calendar.

     * @param Request $request

     * @return \Illuminate\Http\JsonResponse

     * @throws \Illuminate\Validation\ValidationException

     */

    public function index(AttendanceCheckRequest $request)

    {

        return (new AttendanceService())->getAttendance($request);

    }




// //////////////////////////////////new new new new new new

 public function getTeacerSalary(Request $request){
    return (new AttendanceService())->getTeacherSalary($request);
}

public function teacherEnrollActivePassive(Request $request, $id){
        $teacher_enroll = TeacherEnroll::findOrFail($id);
        $teacher_enroll->status = $request->status;
        $teacher_enroll->save();
        $check_ins = json_decode($request->check_in, true);

        foreach($check_ins as $check_in){
            $attendance = Attendance::findOrFail($check_in['id']);
            $attendance->status = $request->status;
            $attendance->save();
        }
        
        if($request->study_mode == 'One to One'){
            if($request->status == '0'){
                $enroll_lock = new TeacherEnrollLocks();
                $enroll_lock->enroll_id = $id;
                $enroll_lock->student_group_id = $request->student_group_id; 
                $enroll_lock->date = $request->date; 
                $enroll_lock->teacher_id = $request->teacher_id;
                $enroll_lock->save();
            }else{
                $enroll_lock_delete = TeacherEnrollLocks::where('enroll_id', $id)->first();
                if( $enroll_lock_delete){
                    $enroll_lock_delete->delete();
                }
                
            }
        }
        if($request->status == '0'){
            $teacher_enrolls = TeacherEnroll::where('student_group_id', $request->student_group_id)
                                ->where('teacher_id', $request->teacher_id)
                                ->where('lesson_mode', $request->lesson_mode)
                                ->where('study_mode', $request->study_mode)->get();
            $check_lock = [];
            foreach( $teacher_enrolls as $item){
                if($item->status == '1'){
                    array_push($check_lock, $item);
                }
            }  
            if(count($check_lock) == 0){
                foreach( $teacher_enrolls as $group){
                    $enroll_lock = new TeacherEnrollLocks();
                    $enroll_lock->enroll_id = $group['id'];
                    $enroll_lock->student_group_id = $group['student_group_id']; 
                    $enroll_lock->date = $request->date; 
                    $enroll_lock->teacher_id = $group['teacher_id'];
                    $enroll_lock->save();
                }  
               
            }                 
            return response()->json(['msg'=> 'Student status changed to Inactive.']);
        }else{
            return response()->json(['msg'=> 'Student status changed to Active.']);
        }

        
    }

    public function teacherGroupActivePassive(Request $request){
         $groups = json_decode($request->group, true);
         $enroll_lock = new TeacherEnrollLocks();

         foreach($groups as $group){
            $teacher_enroll = TeacherEnroll::findOrFail($group['id']);
            $teacher_enroll->status = $request->status;
            $teacher_enroll->save();

            foreach($group['check_in'] as $check_in){
                $attendance = Attendance::findOrFail($check_in['id']);
                $attendance->status = $request->status;
                $attendance->save();
            }
            
            if($request->status == '0'){
                $enroll_lock = new TeacherEnrollLocks();
                $enroll_lock->enroll_id = $group['id'];
                $enroll_lock->student_group_id = $group['student_group_id']; 
                $enroll_lock->date = $request->date; 
                $enroll_lock->teacher_id = $group['teacher_id'];
                $enroll_lock->save();
            }else{
                $enroll_lock_delete = TeacherEnrollLocks::where('enroll_id', $group['id'])->first();
                if( $enroll_lock_delete){
                    $enroll_lock_delete->delete();
                }
                
                
            }
            
         }
        if($request->status == '0'){
            return response()->json(['msg'=> 'The group and all students in the group became Inactive.']);
        }else{
            return response()->json(['msg'=> 'The group and all students in the group became Active.']);
        }
        // $teacher_enroll = TeacherEnroll::findOrFail($id);
        // $teacher_enroll->status = $request->status;
        // $teacher_enroll->save();
        // if($request->status == '0'){
        //     return response()->json(['msg'=> 'Student Locked Successfully.']);
        // }else{
        //     return response()->json(['msg'=> 'Student Unlocked Successfully.']);
        // }
    }


    /** check  Teacher available class for student.

     * @param Request $request

     * @return \Illuminate\Http\JsonResponse

     */

//    /////////////////////////////////////////////////////////////////////////////////////
// /////////////////GET TEACHER DAILY CONTROLE //////////////////////////
    public function getDailyControle(Request $request){

        $daily_checkin = Attendance::where('teacher_id', auth()->user()->id)->whereDate('date', Carbon::today())->get();

        $final_data = [];

        foreach($daily_checkin as $item){

            $cekcins = [];

            $f_name =  User::where('id', $item->user_id)->get();

            if(isset($f_name) && count($f_name) != 0){
              $item->student_name = $f_name[0]->full_name;  
            }else{
                $item->student_name = '';
            }


            $s_format = TeacherEnroll::where('teacher_id', $item->teacher_id)->where('student_id', $item->user_id)->where('lesson_mode', $item->lesson_mode)->get();
            if(isset($s_format) && count($s_format) != 0){
                $item->format = $s_format[0]->study_mode;
            }else{
                $item->format = '';
            }

            // $item->student_name = User::where('id', $item->user_id)->get()[0]->full_name;
            // $item->format = TeacherEnroll::where('teacher_id', $item->teacher_id)->where('student_id', $item->user_id)->where('lesson_mode', $item->lesson_mode)->get()[0]->study_mode;

            $cekcins['student_name'] = $item->student_name;
            $cekcins['format'] = $item->format;
            $cekcins['lesson_mode'] = $item->lesson_mode;

            array_push($final_data, $cekcins);

        }
      
        return $final_data;
    }

    public function checkTeacherAvailableClasses(Request $request)

    {

        $request->validate([

            'teacher_id' => 'required|integer|exists:users,id',

        ]);

        $getToday = date('Y-m-d');

        if (strtotime($request->today) != strtotime($getToday)) {

            return response()->json(['message' => 'Date Time does not match'], 400);

        }

        $class =

            TeacherEnroll::where('teacher_id', $request->teacher_id)->where('student_id', auth()->id())->pluck('lesson_mode');

        return response()->json($class);

    }



    /**

     * Store Student Attendance.

     * @param \Illuminate\Http\Request $request

     * @return \Illuminate\Http\JsonResponse

     */

    public function store(AttendanceRequest $request)

    {

        $user  = User::findOrFail($request->student_id);

        $count = (new AttendanceService())->todayClassCheckIn($request);

        if($user->attendance_lock_status == '1' || $user->lock_status == true || $user->manual_lock_status == '1'){
            return response()->json(['message' => "The student has been locked. You can only check in for unlocked students."], 400);
        }

        if ($count) {

            return response()->json(['message' => 'Attendance has been taken already.'], 400);

        }

        $count_class = (new AttendanceService())->storeAttendance($request);

        if ($count_class >= 14) {

            return response()->json(['message' => 'Your profile will be lock.'], 400);

        }

        if ($count_class == $user->payment_Reminder_min_value) {

            Notification::send($user, new ParentPaymentNotify());

            Notification::send($user->parent, new ParentPaymentNotify());

            return response()->json(['msg' => 'Attendance added successfully.'], 200);

        }

        if ($count_class == $user->payment_Reminder_max_value) {

            $remain_class = $user->payment_Reminder_max_value - $count_class;

            Notification::send($user, new StudentLocked($user, $remain_class));

            Notification::send($user->parent, new StudentLocked($user, $remain_class));


            $user->tokens->each(function ($token, $key) {

                $token->delete();

            });

            return response()->json(['msg' => 'Attendance added successfully.'], 200);

        }

        return response()->json(['msg' => 'Attendance added successfully.'],200);



    }



    /**

     * get check in list following role

     * company_head

     * office manager

     * teacher

     * head teacher

     * parent

     * auditor

     * chief_auditor

     * accountant

     * @param \Illuminate\Http\Request $request

     * @return \Illuminate\Http\JsonResponse

     */

    public function report(AttendanceReportRequest $request)

    {

        return (new AttendanceService())->attendanceReport($request);

    }



    /**

     * Display a listing of the resource.

     * count attendance for student

     *

     * @return \Illuminate\Http\JsonResponse

     */

    public function payment()

    {

        if (auth()->user()->role == 'student') {

            $teachers    =

                User::whereIn('id', User::findOrFail(auth()->user()->id)->teachers->pluck('teacher_id'))->get();

            $teacher_arr = array();

            $kk          = 0;

            foreach ($teachers as $key => $teacher) {

                $data_user =

                    Attendance::where('user_id', auth()->user()->id)->where('teacher_id', $teacher->id)->where('paid', 0)->count();



                if ($data_user >= auth()->user()->payment_Reminder_min_value) {

                    $teacher_arr[$kk]['count'] = $data_user;

                    $teacher_arr[$kk]['name']  = $teacher->full_name;

                    $kk++;

                }

            }

            return response()->json([

                'count' => count($teacher_arr),

                'data'  => $teacher_arr

            ]);

        }



    }



    public function getPaymentEvent(Request $request)

    {

        $paymentNote = PaymentNote::where('student_id', $request->user_id)->get();

        $start_date  = Carbon::parse($request->start)->toDateString();

        $end_date    = Carbon::parse($request->end)->toDateString();



        $events = Attendance::where('user_id', $request->user_id)

            ->whereBetween('date', array($start_date, $end_date))

            ->with('teacher')->get();

        if (auth()->user()->role == 'company_head' || auth()->user()->role == 'office_manager') {

            $array = array_merge($paymentNote->toArray(), $events->toArray());

            return response()->json($array);

        } else {

            return response()->json($events);

        }



    }



    /** student attendance payment and mail send .

     * @param AttendanceSetPaymentRequest $request

     * @return \Illuminate\Http\JsonResponse

     */

    public function setPayment(AttendanceSetPaymentRequest $request)

    {



        $user        = User::findOrFail($request->student_id);

        $class_count = Attendance::where('paid', 0)

            ->where('user_id', $request->student_id)

            ->where('teacher_id', $request->teacher_id)

            ->count();

        Attendance::where('paid', 0)->where('user_id', $request->student_id)->where('teacher_id', $request->teacher_id)->update([

            'amount'     => $request->amount,

            'paid'       => 1,

            'company_id' => auth()->user()->company_id,

        ]);

        if ($class_count >= $user->payment_Reminder_max_value || $class_count <= $user->payment_Reminder_max_value) {

            //send student account email

            if ($user->send_email_status == 1) {

                Notification::send($user, new StudentUnlocked($user));

            }

            //send parent  account email

            Notification::send($user->parent, new StudentUnlocked($user));

            User::findOrFail($request->student_id)->update([

                'attendance_lock_status' => 0

            ]);

        }

        return response()->json(['msg' => 'payment added successfully.']);

    }



    /** Store single payment in attendance db.

     * @param SinglePaymentRequest $request

     * @return \Illuminate\Http\JsonResponse

     */

    public function singlePayment(SinglePaymentRequest $request)

    {

        (new AttendanceService())->singlePayment($request);



        return response()->json(['msg' => 'Payment added successfully']);

    }



    public function getStudentClass(Request $request)

    {

        return Attendance::with('user:id,first_name,last_name', 'teacher:id,first_name,last_name')

                ->where('paid', 0)

                ->where('user_id', $request->student_id)

                ->where('teacher_id', $request->teacher_id)

                ->select('id','user_id','teacher_id')

                ->get();

    }



    /** get teacher available classes .

     * @param Request $request

     * @return \Illuminate\Http\JsonResponse

     */

    public function getTeacherAvailableClasses(Request $request)

    {

        $request->validate([

            'teacher_id' => 'required|integer|exists:users,id',

            'student_id' => 'required|integer|exists:users,id',

        ]);

        // ->where('status', '1')

        $class = TeacherEnroll::where('teacher_id', $request->teacher_id)->where('status', '1')->where('student_id', $request->student_id)->pluck('lesson_mode');

        return response()->json($class);

    }



    /** Store Attendance manually .

     * @param AttendanceManuallyStoreRequest $request

     * @return \Illuminate\Http\JsonResponse

     */

    public function storeAttendanceManually(AttendanceManuallyStoreRequest $request)

    {


        $user = User::findOrFail($request->student_id);

        $count = Attendance::where('user_id', $request->student_id)

            ->where('teacher_id', $request->teacher_id)

            ->where('paid', 0)

            ->where('lesson_mode', $request->lesson_mode)

            ->where('date', $request->today)

            ->count();



        if ($count) {

            return response()->json(['message' => 'Attendance has been taken already.'], 400);

        }

        if ($count >= 12) {

            return response()->json(['message' => 'Student profile has been locked '], 400);

        }



        (new AttendanceService())->storeAttendanceManually($request);


        $attendance_count = Attendance::where('user_id', $request->student_id)

            ->where('teacher_id', $request->teacher_id)

            ->where('paid', 0)

            ->where('lesson_mode', $request->lesson_mode)

            

            ->count();


// new nnew new new new 03/24/2022
         if ($attendance_count >= $user->payment_Reminder_max_value) {

            $user->tokens->each(function ($token, $key) {

                $token->delete();

            });
            

        }

        return response()->json(['msg' => 'Attendance added manually'], 200);

    }



    /** Student Attendance lock status change

     * @param Request $request

     * @return \Illuminate\Http\JsonResponse

     */

    public function studentUnlock(Request $request)

    {

        (new AttendanceService())->studentAttendanceStatusUnlock($request);



        return response()->json(['msg' => 'Student unlock successfully'], 200);

    }

}

