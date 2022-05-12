<?php



namespace App\Services;



use App\TeacherEnroll;

use App\User;

use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
use App\Exam;
use App\StudentExam;
use Illuminate\Support\Str;
use App\TeacherLock;
class UserService

{

    private $request;

    private $user;



    /**

     * UserService constructor.

     * @param $request

     */

    public function __construct($request)

    {

        $this->request = $request;

        $this->user_type = $this->request->user_type;

        $this->user = $this->getUsers();



    }



    /**

     * Base filter of user

     *

     * @param $request

     * @return \Illuminate\Database\Eloquent\Builder

     */

    private function getUsers()

    {

        //Use this condition for check user type is Headtecher/Teacher or Not

        if (auth()->user()->role == "teacher" || auth()->user()->role == "head_teacher" || auth()->user()->role == "speaking_teacher") {

            $user = User::query()->orderBy('first_name', 'ASC');

        } else {

            $user = User::query();

        }



        if ($this->request->keyword != '') {

            $user->where(function ($query) {

                $query->where(DB::raw("concat(first_name,' ',last_name)"), 'like', "%{$this->request->keyword}%")

                    ->orwhere('email', 'like', "%{$this->request->keyword}%")

                    ->orWhere('phone_number', 'like', "%{$this->request->keyword}%")

                    ->orWhere('role', 'like', "%{$this->request->keyword}%")

                    ->orWhereHas('parent', function ($q) {

                        $q->where(DB::raw("concat(first_name,' ',last_name)"), 'like', "%{$this->request->keyword}%");

                    })
                    ->orWhereHas('children', function ($q) {

                        $q->where(DB::raw("concat(first_name,' ',last_name)"), 'like', "%{$this->request->keyword}%");

                    })

                    ->orWhere('date_of_birth', 'like', "%{$this->request->keyword}%");

            });

        }

        if ($this->request->lockedFilter == 'Locked') {

            $user->where(function ($query) {

                $query->where('manual_lock_status', 1)->orWhere('attendance_lock_status', 1);

            });

        }

        if ($this->request->lockedFilter == 'Unlocked') {

            $user->where(function ($query) {

                $query->where('manual_lock_status', '!=', 1)->Where('attendance_lock_status', '!=', 1);

            });



        }

        if ($this->request->lessonFilter != '') {

            $user->whereHas('studentEnrollClass', function ($q) {

                $q->where('lesson_mode', 'like', "%{$this->request->lessonFilter}%");

            });



        }

        if (isset($this->request->schoolYearFilter) && $this->request->schoolYearFilter != '') {



            $user->where(function ($query) {



                $query->where('school_year', $this->request->schoolYearFilter);



            });



        }

        return $user->latest('id');

    }



    /**

     * Get all

     *

     * @param $request

     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection

     */

    public function allUsers()

    {

        return $this->user->where(function ($query) {

            $query->where('role', '!=', 'super_admin');

        })->get();

    }



    /**

     * Filter by staff role

     *

     * @param $request

     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator

     */

    public function getStaff()

    {



        return $this->user->where(function ($query) {

            $query->whereIn('role', ['chief_auditor', 'auditor', 'content_manager', 'office_manager', 'teacher_manager', 'accountant', 'content_master']);

        })->take($this->request->query('page')*20)->get();

    }



    /**

     * Filter by company_head role

     *

     * @param $request

     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection

     */

    public function getCompanyHead()

    {

        return $this->request->query('page') ? $this->user->with('company')->where(function ($query) {

            $query->where('role', 'company_head');

        })->take($this->request->query('page')*20)->get() : $this->user->where(function ($query) {

            $query->where('role', 'company_head');

            $query->whereNull('company_id');

            if ($this->request->company_head_id) {

                $query->orwhere('id', $this->request->company_head_id);

            }

        })->get([DB::raw("concat(first_name,' ',last_name) AS text"), DB::raw("id AS value")]);

    }



    /**

     * Filter by teacher role

     *

     * @param $request

     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection

     */

    public function getCompanyHeadTeacher()

    {

        $teachers = User::where('company_id', auth()->user()->company_id)->whereIn('role', ['teacher', 'head_teacher', 'speaking_teacher'])->pluck('id');
        $locked_teachers = TeacherLock::whereIn('teacher_id', $teachers)->where('lock_status', '1')->pluck('teacher_id');

        $data = $this->request->query('page') && auth()->user()->role == 'company_head' || $this->request->query('page') && auth()->user()->role == 'office_manager' ?


            
            $this->user->whereNotIn('id', $locked_teachers)->whereIn('role', ['head_teacher', 'teacher', 'speaking_teacher'])->where(function ($query) {



                if ($this->request->company_id) {



                    $query->where('company_id', $this->request->company_id);



                }

                

            })->take($this->request->query('page') * 20)->get() :


            $this->request->query('page') && auth()->user()->role == 'super_admin'?

            $this->user->with('teacherLock')->whereIn('role', ['head_teacher', 'teacher', 'speaking_teacher'])->where(function ($query) {



                if ($this->request->company_id) {



                    $query->where('company_id', $this->request->company_id);



                }

                

            })->take($this->request->query('page') * 20)->get() :

            $this->user->with('teacherLock')->whereIn('role', ['head_teacher', 'teacher', 'speaking_teacher'])->where(function ($query) {

                if ($this->request->company_id) {

                    $query->where('company_id', $this->request->company_id);

                }

            })->get();



        return $data;



    }



    /**

     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator

     */

    public function getofficeManager()

    {



        return $this->user->whereIn('role', ['office_manager'])->where(function ($query) {

            if ($this->request->company_id) {

                $query->where('company_id', $this->request->company_id);

            }

            if ($this->request->keyword != '') {

                $query->where(DB::raw("concat(first_name,' ',last_name)"), 'like', "%{$this->request->keyword}%")

                    ->orwhere('email', 'like', "%{$this->request->keyword}%")

                    ->orWhere('phone_number', 'like', "%{$this->request->keyword}%")

                    ->orWhere('role', 'like', "%{$this->request->keyword}%")

                    ->orWhereHas('parent', function ($q) {

                        $q->where(DB::raw("concat(first_name,' ',last_name)"), 'like', "%{$this->request->keyword}%");

                    })

                    ->orWhere('date_of_birth', 'like', "%{$this->request->keyword}%");

            }

        })->take($this->request->query('page') * 20)->get();

    }



    /**

     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection

     */

    public function getCourseEnrollTeacher()

    {



        if (auth()->user()->role == 'parent') {



            $teachers_ids = array();

            $user = User::with('children.teachers')->findOrFail(auth()->id());

            foreach ($user->children as $key => $children) {

                foreach ($children->teachers as $key => $teacher) {

                    $teachers_ids[] = $teacher->teacher_id;

                }

            }

            $teachers_ids = array_unique($teachers_ids);

            return $this->user->whereIn('id', $teachers_ids)->get();



        } else {

            // return $this->user->whereIn('role', ['head_teacher', 'teacher'])->where(function ($query) {

            //     if ($this->request->company_id) {

            //         $query->where('company_id', $this->request->company_id);

            //     }

            // })->get();

            $teacherArray = [];
            $teachers = $this->user->whereIn('role', ['head_teacher', 'teacher', 'speaking_teacher'])->where(function ($query) {
                if ($this->request->company_id) {
                    $query->where('company_id', $this->request->company_id);
                }
            })->get();

            foreach ($teachers as $teacher) {
                $lockStatus = User::with('teacherLock')->findOrFail($teacher->id);
                array_push($teacherArray,  $lockStatus);
            }

            return $teacherArray;

        }

    }



    /**

     * Filter by student role

     *

     * @param $request

     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator

     */

    public function getCompanyHeadStudent()

    {



        if ($this->request->assign_to_user) {

            // Teacher assign student

            // $get_assign_student = TeacherEnroll::where('teacher_id', $this->request->assign_to_user)->pluck('student_id')->toArray();

            // $sql =  $this->user->with(['studentEnrollClass:id,student_id,lesson_mode'])->whereIn('id', array_unique($get_assign_student))->where('role', 'student')->where(function ($query) {

            //     if ($this->request->company_id) {

            //         $query->where('company_id', $this->request->company_id);

            //     }

            // });

            // return $this->request->query('page') ? $sql->take($this->request->query('page') *20)->get() : $sql->get();

            $date = Carbon::now();

            // Teacher assign student


            // where('status', 1)
            $get_assign_student = TeacherEnroll::where('teacher_id', $this->request->assign_to_user)->where('status','1')->pluck('student_id')->toArray();

            

            $sql =  $this->user->with(['studentEnrollClass:id,student_id,lesson_mode'])->whereIn('id', array_unique($get_assign_student))->where('role', 'student')->where(function ($query) {



                if ($this->request->company_id) {



                    $query->where('company_id', $this->request->company_id);



                }

                

            })
            ->withCount(['answers'=>function ($q) use ($date) {
                $q->whereMonth('submit_date', $date->format('m'))->whereYear('submit_date', $date->format('Y'));
            }])
            ->withCount(['speakingAnswers'=>function ($q) use ($date) {
                $q->whereMonth('created_at', $date->format('m'))->whereYear('created_at', $date->format('Y'));
            }])->take($this->request->page *20)->get();
            // $this->request->query('page') ? $sql->take($this->request->query('page') *20)->get(): $sql->get();
            // if(isset($this->request->page) && $this->request->page != ''){
            //     return $sql->take($this->request->page *20)->get();
            // }else{
            //     return $sql->get();
            // }


            
            // where('status', 1)
            foreach($sql as $item){
                $item->student_lesson_mode = TeacherEnroll::where('teacher_id', $this->request->assign_to_user)->where('status', '1')->where('student_id', $item->id)->get();
                $general_hw = [];

                $SAT_hw = [];
            

                $homeWorksForAll = StudentExam::where('is_submit', 1)


                    ->where('student_id', $item->id)   /// muelimin butun telebelerinin imtahnlari gelir



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
                        $hw->student_lesson_mode = TeacherEnroll::where('teacher_id', $this->request->assign_to_user)->where('student_id', $hw->student_id)->get();

                        foreach($hw->student_lesson_mode as $items){
                            if(Str::contains($items->lesson_mode, 'English') && Str::contains($hw->course_title, 'English')){
                                array_push($general_hw, $items);
                            }else if(Str::contains($items->lesson_mode, 'SAT') && Str::contains($hw->course_title, 'SAT')){
                                array_push($SAT_hw, $items);
                            }
                        }
                        
                    }


                
                    $item['monthly_sat_homework_taken'] = count($SAT_hw);

                    $item['monthly_general_english_homework_taken'] = count($general_hw);


                    $general_exams = [];

                    $SAT_exams = [];

                    $examsForAll = StudentExam::where('is_submit', 1)


                        ->where('student_id', $item->id)



                        ->whereMonth('updated_at', $date->format('m'))->whereYear('updated_at', $date->format('Y'))



                        ->where('duration', '>', 0)



                        ->where('score', '>=', 70)->get();
                      
                        foreach($examsForAll as $exam){

                            // array_push($exam_results, Exam::where('id', '=', $exam->exam_id)->get());

                            $exam->exam = Exam::where('id', '=', $exam->exam_id)->get();    //// telebenin verdiyi imtahan 
                            foreach($exam->exam as $items){
                                $exam->course_title = DB::table('courses')->where('id', '=', $items->course_id)->get()[0]->title;
                            }
                            $exam->student_lesson_mode = TeacherEnroll::where('teacher_id', $this->request->assign_to_user)->where('student_id', $item->id)->get();
                           
                            foreach($exam->student_lesson_mode as $items){
                                if(Str::contains($items->lesson_mode, 'English') && Str::contains($exam->course_title, 'English')){
                                    array_push($general_exams, $items);
                                }else if(Str::contains($items->lesson_mode, 'SAT') && Str::contains($exam->course_title, 'SAT')){
                                    array_push($SAT_exams, $items);
                                }
                            }
                        }
                        
                        
                        $item['monthly_general_english_exams_taken'] = count($general_exams);

                        $item['monthly_sat_exam_takens'] = count($SAT_exams);
            }


           return $sql;

        } else {

            if ($this->request->company_id) {

                // company  head

                $array = array('role' => 'student', 'company_id' => $this->request->company_id);

            } else {

                // super admin

                $array = array('role' => 'student');

            }

            return $this->user->with(['studentEnrollClass:id,student_id,lesson_mode'])->where($array)->take($this->request->query('page') *20)->get();

        }

    }



    /** get Celt Student

     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator

     */

    public function celtStudents(){

        if(auth()->user()->role == 'super_admin'){
            $array = array('role' => 'student');



            return $this->user->with(['studentEnrollClass:id,student_id,lesson_mode'])->where($array)->get();
        }else {
            $array = array('role' => 'student', 'company_id' => $this->request->company_id);

            if(isset($this->request->page)){
                return $this->user->with(['studentEnrollClass:id,student_id,lesson_mode'])->where($array)->take($this->request->page*20)->get();
            }else{
                return $this->user->with(['studentEnrollClass:id,student_id,lesson_mode'])->where($array)->get();
            }

            // return $this->user->with(['studentEnrollClass:id,student_id,lesson_mode'])->where($array)->get();
        }
        // $array = array('role' => 'student', 'company_id' => $this->request->company_id);

        // return $this->user->with(['studentEnrollClass:id,student_id,lesson_mode'])->where($array)->get();

    }



    /**

     * Filter by student role

     *

     * @param $request

     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection

     */

    public function getParents()

    {

        // $sql = $this->user->where('role', 'parent')

        // ->where('company_id', $this->request->company_id);

        // return $sql->get();



        return $this->request->query('page') ?

            $this->user->where('role', 'parent')->where(function ($query) {

                if ($this->request->company_id) {

                    $query->where('company_id', $this->request->company_id);

                }

            })->take($this->request->query('page') * 20)->get() :

            $this->user->where('role', 'parent')->where(function ($query) {

                if ($this->request->company_id) {

                    $query->where('company_id', $this->request->company_id);

                }

            })->get();

    }



    /**

     * Filter by parent student

     *

     * @param $request

     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection

     */

    public function getParentsStudents()

    {

        $sql = $this->user->where('role', 'student')->whereHas('parent', function ($query) {

            $query->where('id', auth()->id());

        })->select('id','email','phone_number','first_name','last_name');



        return $this->request->query('page') ?

            $sql->paginate(10) :

            $sql->get();

    }



    /**api end point for CELT APP.

     * get parent student with certificate

     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection

     */

    public function parentStudentCertificate(){



        $sql = $this->user->with('certificates')->where('role', 'student')->whereHas('parent', function ($query) {

            $query->where('id', auth()->id());

        })->select('id','email','phone_number','first_name','last_name');



        return $this->request->query('page') ? $sql->paginate(10) : $sql->get();

    }

    /**

     * Filter teachers

     *

     * @param $request

     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection

     */

    public function getTeachers()

    {

        $data = $this->user->select('first_name', 'id', 'last_name', 'company_id')

            ->with('company:id,name')

            ->withCount('teacherStudents')

            ->whereIn('role', ['teacher', 'head_teacher', 'speaking_teacher'])->where(function ($query) {

                if ($this->request->query('company_id')) {

                    $query->where('company_id', $this->request->query('company_id'));

                }

            })->get();



        $item = collect($data);

        return $item->values()->all();



    }





}

