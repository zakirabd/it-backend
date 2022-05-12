<?php



namespace App\Http\Controllers;



use App\Company;

use App\Exports\StudentExport;

use App\Exports\UsersExport;

use App\Helpers\UploadHelper;

use App\Http\Requests\EmailPasswordRequest;

use App\Http\Requests\UserRequest;

use App\Notifications\VerifyEmail;

use App\PaymentLog;

use App\Services\UserService;

use App\TeacherEnroll;

use App\TeacherLock;

use App\User;

use Cache;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Storage;

use App\Attendance;

use Maatwebsite\Excel\Facades\Excel;

use phpDocumentor\Reflection\Types\Collection;


use App\Http\Requests\StudentFormRequests;

use App\StudentForm;

use App\StudentsLog;


use Illuminate\Support\Facades\Mail;


use App\Mail\TestMailSend;

class UserController extends Controller

{

    /**

     * Display a listing of the resource.

     *

     * @param Request $request

     * @return \Illuminate\Http\JsonResponse

     */





    public function index(Request $request)

    {



        if ($request->query('user_type') && $request->query('user_type') == 'company_head') {

            $users = (new UserService($request))->getCompanyHead();

        } else if ($request->query('user_type') && $request->query('user_type') == 'company_head_teacher') {

            $users = (new UserService($request))->getCompanyHeadTeacher();

        } else if ($request->query('user_type') && $request->query('user_type') == 'techer_gpa') {

            $users = (new UserService($request))->getTeacherGpa();

        } else if ($request->query('user_type') && $request->query('user_type') == 'course_enroll_teacher') {

            $users = (new UserService($request))->getCourseEnrollTeacher();

        } else if ($request->query('user_type') && $request->query('user_type') == 'office_manager') {

            $users = (new UserService($request))->getofficeManager();

        } else if ($request->query('user_type') && $request->query('user_type') == 'company_head_student') {

            $users = (new UserService($request))->getCompanyHeadStudent();

        } else if ($request->query('user_type') && $request->query('user_type') == 'parent') {

            $users = (new UserService($request))->getParents();

        } else if ($request->query('user_type') && $request->query('user_type') == 'parent_student') {

            $users = (new UserService($request))->getParentsStudents();

        } else if ($request->query('user_type') && $request->query('user_type') == 'parent_student_certificate') {

            /* api end point for celt mobile App */

            $users = (new UserService($request))->parentStudentCertificate();

        } else if ($request->query('user_type') && $request->query('user_type') == 'teacher_manager_teachers') {

            $users = (new UserService($request))->getTeachers();

        } else if ($request->query('user_type') && $request->query('user_type') == 'celt_students') {

            $users = (new UserService($request))->celtStudents();

        } else if ($request->query('user_type') && $request->query('user_type') == 'all') {

            $users = (new UserService($request))->allUsers();

        } else {

            $users = (new UserService($request))->getStaff();

        }

        return response()->json($users);

    }



    public function getAllStudents(Request $request){

        $users = (new UserService($request))->AllStudents();

        return response()->json($users);

    }
    
    public function getCompanyHeadLockedTeacher(Request $request){
        $teachers = User::where('company_id', auth()->user()->company_id)->whereIn('role', ['teacher', 'head_teacher', 'speaking_teacher'])->pluck('id');
        $locked_teachers = TeacherLock::whereIn('teacher_id', $teachers)->where('lock_status', '1')->pluck('teacher_id');
        $user = User::whereIn('id', $locked_teachers);
        
        if($request->keyword != ''){
             $user->where(function ($query) use ($request) {



                $query->where(DB::raw("concat(first_name,' ',last_name)"), 'like', "%{$request->keyword}%")



                    ->orwhere('email', 'like', "%{$request->keyword}%")



                    ->orWhere('phone_number', 'like', "%{$request->keyword}%")



                    ->orWhere('role', 'like', "%{$request->keyword}%");

            });
        }
       
        return $user->take(20 * $request->page)->get();
    }

    public function sendHolidayMail(){
        // $arr = [];

        // $users = User::where('role', 'student')->where('company_id', 2)->orWhere('role', 'parent')->where('company_id', 2)->get();
        // // Mail::to('alili@celt.az')->send(new TestMailSend());
        // foreach($users as $user){
        //     Mail::to($user->email)->send(new TestMailSend());
        // }
        // return $users;
        // $user = User::where('email', 'zakir_abdurahimov@mail.ru')->first();
        //  
        //  return 'success';
        // $users = User::get();
        // $inc = 0;
        // $fin = [];
        // foreach($users as $user){
        //     if($inc >= 6100 && $inc <6102){
        //             Mail::to($user->email)->send(new TestMailSend($user));
        //         array_push($fin, $user);
        //     }
        //     $inc++;
        // }
        // return $fin;
    }

    /**

     * Store a newly created resource in storage.

     *

     * @param \Illuminate\Http\Request $request

     * @return \Illuminate\Http\JsonResponse

     */
   ///////////////////  insert student form datas///////////////////////////////////////////////
    public function storeForm(Request $request){
        
        $form = new StudentForm();

        $form->fill($request->all());

        $form->save();
      
        return response()->json(['msg' => 'Form Created Successfully']);
    }
    // ////////////////////////update student form datas////////////////////////////////////////

    public function updateForm(Request $request){
        
        $form = StudentForm::findOrFail($request->id);

        $form->fill($request->all());

        $form->save();
      
        return response()->json(['msg' => 'Form Updated Successfully']);

    }
    // //////////////////////////////// get student form data //////////////////////////////////
    public function getFormData(Request $request){
         $studentForm = StudentForm::where('company_id', auth()->user()->company_id)->get();
        return $studentForm;
    }





    // /////////////////////////////////////////////
    public function store(UserRequest $request)

    {

        $send_email_status = $request->send_email_status;

        if (isset($send_email_status) && $send_email_status == 'true') {

            $send_email_status = 1;



        } else {

            $send_email_status = 0;

        }

        $user = new User();

        $user->fill($request->all());

        $user->send_email_status = $send_email_status;

        $user->password = bcrypt($request->password);

        if ($request->hasFile('avatar_url')) {

            $user->avatar_url = UploadHelper::imageUpload($request->file('avatar_url'), 'avatars');

        }

         // ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// ////////////////////NEW LINE FOR MAX AND MIN REMINDER VALUE AND SCHOOL YEAR ////////////////////////////////////
        if(isset($request->payment_Reminder_max_value) && isset($request->payment_Reminder_min_value)){
            $user->payment_Reminder_max_value = $request->payment_Reminder_max_value;
            $user->payment_Reminder_min_value = $request->payment_Reminder_min_value;
        }

        if(isset($request->school_year)){
            $user->school_year = $request->school_year;
        }

        $user->save();

        $user->notify(new VerifyEmail($user));



        if ($request->parent) {

            $user->parent()->attach(explode(',', $request->parent));

        }

         if ($request->child) {

            $user->child()->attach(explode(',', $request->child));

        }

        if ($user->role == 'company_head') {

            Company::where('id', $request->company_id)->update(['user_id' => $user->id]);

        }



        return response()->json(['msg' => 'User created successfully.', 'data' => $user]);

    }



    /**

     * Display the specified resource.

     *

     * @param $id

     * @return \Illuminate\Http\JsonResponse

     */

    public function show($id)

    {

        $user = User::with('company')->findOrFail($id);

        $user->parent = $user->parent()->pluck('id');



        return response()->json($user);

    }



    /**

     * Update the specified resource in storage.

     *

     * @param \Illuminate\Http\Request $request

     * @param int $id

     * @return \Illuminate\Http\JsonResponse

     */

    public function update(UserRequest $request, $id)

    {



        $send_email_status = $request->send_email_status;

        if (isset($send_email_status) && $send_email_status == 'true') {

            $send_email_status = 1;



        } else {

            $send_email_status = 0;

        }



        $user = User::findOrFail($id);



        $user->fill($request->except(['lock_status']));

        //Todo : lock_status



        //  $user->fill($request->all());

        $user->send_email_status = $send_email_status;



        if ($request->has('password')) {

            $user->password = bcrypt($request->password);

        }

        if ($request->hasFile('avatar_url')) {

            $user->avatar_url = UploadHelper::imageUpload($request->file('avatar_url'), 'avatars');

        }

        // ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// ////////////////////NEW LINE FOR MAX AND MIN REMINDER VALUE and SCHOOL YEAR ////////////////////////////////////
       if(isset($request->payment_Reminder_max_value) && isset($request->payment_Reminder_min_value)){
            $user->payment_Reminder_max_value = $request->payment_Reminder_max_value;
            $user->payment_Reminder_min_value = $request->payment_Reminder_min_value;
        }

        if(isset($request->school_year)){
            $user->school_year = $request->school_year;
        }
        $user->save();



        if ($request->parent) {

            $user->parent()->sync(explode(',', $request->parent));

        }

        if ($request->child) {

            $user->child()->sync(explode(',', $request->child));

        }

        if ($user->role == 'company_head') {

            Company::where('id', $request->company_id)->update(['user_id' => $user->id]);

        }



        return response()->json(['msg' => 'User updated successfully.']);

    }



    /**

     * Remove the specified resource from storage.

     *

     * @param int $id

     * @return \Illuminate\Http\JsonResponse

     */

    public function destroy($id)

    {

        $user = User::findOrFail($id);

        if ($user->avatar_url) {

            Storage::delete('public/' . $user->avatar_url);

        }

        $user->delete();

        return response()->json(['msg' => 'User has been deleted successfully.']);

    }



    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\JsonResponse

     */

    public function resendVerifyEmail()

    {



        auth()->user()->notify(new VerifyEmail(auth()->user()));



        return response()->json(['msg' => 'Email sent successfully.']);

    }



    /**

     * Update the specified resource in storage.

     *

     * @param \Illuminate\Http\Request $request

     * @return \Illuminate\Http\JsonResponse

     */

    public function emailPasswordUpdate(EmailPasswordRequest $request)

    {



        $user = User::findOrFail(auth()->id());



        if ($request->type == 'email') {

            $user->email = $request->email;

        } else {

            $user->password = bcrypt($request->new_password);

        }



        $user->save();



        return response()->json(['msg' => $request->type == 'email' ? 'Email updated successfully.' : 'Password updated successfully.']);

    }



    /**

     * Display a listing of the resource.

     *

     * @param int $id

     * @return \Illuminate\Http\JsonResponse

     */

    public function courses($id)

    {

        $request = \request();



        $courses = User::findOrFail($id)

            ->courses()

            ->where(function ($q) use ($request) {

                $q->where('title', 'like', "%{$request->keyword}%");

            })->get();



        return response()->json($courses);

    }



    /**

     * Display a listing of the resource.

     *

     * @param int $user_id

     * @param int $course_id

     * @param \Illuminate\Http\Request $request

     * @return \Illuminate\Http\JsonResponse

     */

    public function courseAttachOrDetach($user_id, $course_id, Request $request)

    {

        // Validate form data

        $request->validate([

            'type' => 'required|string|in:attach,detach'

        ]);



        $user = User::findOrFail($user_id);

        if ($request->type == 'attach') {

            $student_log = new StudentsLog();


            $student_log->student_id = $user_id;

            $student_log->type = 'add';

            $student_log->enroll_type = 'course';

            $student_log->course_id = $course_id;

            
            $student_log->save();


            $user->courses()->syncWithoutDetaching($course_id);

        } else {

            $student_log = new StudentsLog();


            $student_log->student_id = $user_id;

            $student_log->type = 'remove';

            $student_log->enroll_type = 'course';

            $student_log->course_id = $course_id;

            
            $student_log->save();
            

            $user->courses()->detach($course_id);

        }



        return response()->json(['msg' => $request->type == 'attach' ? 'Course assigned successfully.' : 'Course removed successfully.']);

    }



    /**

     * Display a listing of the resource.

     *

     * @param Request $request

     * @return \Illuminate\Http\JsonResponse

     */

    public function teachers(Request $request)

    {

         // student role get assign teachers

        if (auth()->user()->role == 'student') {

            $teachers = User::withCount('locked_teacher')->find(auth()->user()->teachers->pluck('teacher_id'));

            return response()->json($teachers);



        } else {

            $teachers = $request->user_id ? User::find(User::findOrFail($request->user_id)->teachers->pluck('teacher_id')) : User::find(auth()->user()->teachers->pluck('teacher_id'));



            return response()->json($teachers);

        }





    }



    /**

     * Display a listing of the resource.

     *

     * @param Request $request

     * @return \Illuminate\Http\JsonResponse

     */



    public function attendances(Request $request)

    {



        if (auth()->user()->role == 'company_head' || auth()->user()->role == 'office_manager') {

            $get_students = User::CompanyAllStudent(auth()->user()->company_id)->get();



        } else {

            $get_assign_student = TeacherEnroll::where('teacher_id', auth()->user()->id)->pluck('student_id')->toArray();

            $get_students = User::whereIn('id', array_unique($get_assign_student))->get();

        }

        $data = array();

        $index = 0;

        foreach ($get_students as $key => $student) {

            $student_teachers = DB::table('attendances')->where('user_id', $student->id)->where('paid', 0)->groupBy('teacher_id')->count();

            $teachers = User::whereIn('id', Attendance::where('user_id', $student->id)->where('paid', 0)->pluck('teacher_id'))

                ->select(

                    'id',

                    'first_name',

                    'last_name',

                    'payment_Reminder_max_value',

                    'payment_Reminder_min_value'

                )->get();

            if ($student_teachers >= $student->payment_Reminder_min_value) {

                $data[$index]['class_count'] = $student_teachers;

                $data[$index]['student'] = $student;

                $i = 0;

                $lock_count = 0;

                foreach ($teachers as $key => $teacher) {



                    $teacher_attencence_count = Attendance::where('paid', 0)->where('user_id', $student->id)->where('teacher_id', $teacher->id)->count();

                    $remainder = (int)$student->payment_Reminder_min_value;

                    $remainder_max = (int)$student->payment_Reminder_max_value;

                    if ($remainder <= $teacher_attencence_count) {

                        $data[$index]['teachers']['attend'][$teacher->id] = $teacher_attencence_count;

                        $data[$index]['teachers']['info'][$i] = $teacher;

                        if ($remainder_max <= $teacher_attencence_count) {

                            $lock_count = ++$teacher_attencence_count;

                        } else {

                            $lock_count = $teacher_attencence_count;

                        }

                        $data[$index]['teachers']['position'] = $lock_count;

                        $i++;

                    }

                }

                $index++;

            }

        }

        array_multisort(array_map(function ($data) {

            return $data['teachers']['position'];

        }, $data), SORT_DESC, $data);



        return $data;



    }



    /**

     * Make payment.

     *

     * @param Request $request

     * @return \Illuminate\Http\JsonResponse

     */

    public function payment(Request $request)

    {

        // Validate form data

        $request->validate([

            'note' => 'nullable|string'

        ]);



        $user = User::findOrFail($request->id);

        $user->attendances()->where('paid', 0)->latest()->take(12)->update(['paid' => 1]);



        $data = $request->all();

        $data['date'] = now();

        $data['user_id'] = $request->id;

        $data['company_id'] = $user->company_id;

        PaymentLog::create($data);

        return response()->json(['msg' => 'Payment made successfully.']);

    }



    /**

     * Payment log.

     *

     * @param Request $request

     * @return \Illuminate\Http\JsonResponse

     */

    public function paymentLog(Request $request)

    {



        $paymentLog = Attendance::with('user')->where(function ($q) use ($request) {

            $q->where('date', 'like', "%{$request->keyword}%")

                ->orWhereHas('user', function ($q) use ($request) {

                    $q->where(DB::raw("concat(first_name,' ',last_name)"), 'like', "%{$request->keyword}%");

                });

        })

            ->whereIn('user_id', User::where('company_id', $request->company_id)->pluck('id'))

            ->orderBy('paid', 'asc')

            ->latest()

            ->paginate(10);



        return response()->json($paymentLog);

    }



    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\JsonResponse

     */

    public function paymentByParent()

    {

        $children = auth()

            ->user()

            ->children()

            ->with(['attendances' => function ($q) {

                $q->where('paid', 0)

                    ->latest();

            }])

            ->get();



        return response()->json($children);

    }



    public function getStudentTeacher(Request $request)

    {

        $teachers = User::find(User::findOrFail($request->student_id)->teachers->pluck('teacher_id'));

        return response()->json($teachers);



    }



    /** Update user Manual lock status

     * @param User $user

     * @param Request $request

     * @return \Illuminate\Http\JsonResponse

     */

    public function userManualLockStatus(User $user,Request $request)

    {

        $msg ='';

        if($request->type == 'lock'){

            $user->manual_lock_status = 1;

            $msg = 'Student Lock  successfully.';

            $user->tokens->each(function ($token, $key) {

                $token->delete();

            });

        }

        if($request->type == 'unlock'){

            $user->manual_lock_status = 0;

            $msg = 'Student Unlock  successfully.';

        }

        $user->save();

        return response()->json(['msg' =>$msg]);

    }



    /**

     * @param Request $request

     * @return \Illuminate\Http\JsonResponse

     */

    public function lockUnlockTeacher(Request $request)

    {



       $getToday = date('Y-m-d');

        TeacherLock::updateOrCreate(

            [

                'teacher_id' => $request->user_id,



            ],

            [



                'teacher_id' => $request->user_id,

                'lock_status' => $request->lock_status,

                'date' => $getToday

            ]



        );

        if ($request->input('lock_status') == 1) {

            return response()->json(['msg' => 'Teacher lock successfully'],200);



        } else if ($request->input('lock_status') == 0) {

            return response()->json(['msg' => 'Teacher unlock successfully'],200);



        }

    }



    public function userOnlineStatus()

    {

        $users = DB::table('users')->where('role', 'student')->get();

        $users_active = array();

        foreach ($users as $user) {

            if (Cache::has('user-is-online' . $user->id)) {

                array_push($users_active, $user);

            }

        }

        return response()->json(count($users_active));

    }





    public function studentExport(Request $request)

    {

        return Excel::download(new UsersExport($request), 'users.xlsx');

    }



    /** get teacher lock info for user.

     * @return \Illuminate\Http\JsonResponse

     */

    public function userInfo(){



        $user = User::with('teacherLock')->findOrFail(auth()->user()->id);

        return response()->json($user);



    }





}

