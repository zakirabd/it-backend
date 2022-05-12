<?php





namespace App\Services;





use App\Course;

use App\Essay;

use App\Lesson;

use App\Speaking;

use App\SpeakingAnswer;

use App\SpeakingReviews;

use App\TeacherEnroll;

use App\User;

use Illuminate\Support\Facades\DB;

use Nyholm\Psr7\Request;

// new 
use Illuminate\Support\Str;

class SpeakingService

{



    private $request;

    private $speaking;

    private $speakingAnswer;



    public function __construct($request)

    {

        $this->request  = $request;

        $this->speaking = $this->getSpeaking();

        $this->speakingAnswer = $this->speakingAnswers();

    }



    /**

     * Filter by staff role

     *

     * @param $request

     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator

     */

    public function getSpeaking()

    {

        $sql = Speaking::with('course', 'lesson')->latest();

        if ($this->request->keyword != '') {

            $sql->where('title', 'like', "%{$this->request->keyword}%")

                ->orWhere('speaking_type', 'like', "%{$this->request->keyword}%")

                ->orWhereHas('course', function ($course) {

                    $course->where('title', 'like', "%{$this->request->keyword}%");

                });

        }

        if ($this->request->course_id) {

            $sql->WhereHas('course', function ($course) {

                $course->where('id', 'like', "%{$this->request->course_id}%");

            });

        }



        return $sql->take($this->request->page*20)->get();

    }



    /**

     * @return \Illuminate\Database\Eloquent\Builder

     */

    private function speakingAnswers()

    {

        $speakingAnswer = SpeakingAnswer::with('speaking', 'user', 'review')->latest();

        if ($this->request->keyword != '') {

            $speakingAnswer->where(function ($query) {

                $query->whereHas('user', function ($q) {

                    $q->where(DB::raw("concat(first_name,' ',last_name)"), 'like', "%{$this->request->keyword}%")

                        ->orWhere('email', 'like', "%{$this->request->keyword}%");

                })->orWhereHas('speaking', function ($q) {

                        $q->where('title', 'like', "%{$this->request->keyword}%");

                    });

            });

        }

        return $speakingAnswer;

    }



    public function getSpeakingStudentAnswerById()

    {

        $student_id = $this->request->student_id;

        $sql        = SpeakingAnswer::with('speaking', 'user', 'review')->where('status', 1)->latest();

        $sql->WhereHas('user', function ($user) use ($student_id) {

            $user->where('id', $student_id);

        });

        if ($this->request->keyword) {

            $sql->WhereHas('speaking', function ($speaking) {

                $speaking->where('title', 'like', "%{$this->request->keyword}%");

            });

        }

        return $sql->take($this->request->page * 20)->get();

    }



    /** Speaking answer list

     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator

     */

    public function getSpeakingStudentAnswer()

    {



        if ($this->request->type == 'company_head') {

            $get_company_students = User::UnlockStudent(auth()->user()->company_id)->pluck('id');

            return $this->speakingAnswer->whereIn('user_id', $get_company_students)->take($this->request->page*20)->get();

        }

        if (auth()->user()->role == 'student') {

            if($this->request->page !== 'all'){

                return  $this->speakingAnswer->where('user_id', auth()->id())->take($this->request->page*20)->get();

            }else if($this->request->page == 'all'){

                return  $this->speakingAnswer->where('user_id', auth()->id())->get();

            }

            

        }else if(auth()->user()->role == 'parent' ){
            if($this->request->page !== 'all'){


                return  $this->speakingAnswer->where('user_id', $this->request->student_id)->take($this->request->page*20)->get();


            }else if($this->request->page == 'all'){


                return  $this->speakingAnswer->where('user_id', $this->request->student_id)->get();


            }
        }

        if (auth()->user()->role == 'teacher' || auth()->user()->role == 'head_teacher' || auth()->user()->role == 'speaking_teacher') {

            if ($this->request->key == 1) {

                // celt Speaking

                $get_company_students = User::UnlockStudent(auth()->user()->company_id)->pluck('id');

                return $this->speakingAnswer->whereIn('user_id', $get_company_students)->take($this->request->page*20)->get();

            } else {

                // assign student speaking review

                $get_assign_student = TeacherEnroll::where('teacher_id', auth()->user()->id)->where('status', '1')->pluck('student_id')->toArray();

                return $this->speakingAnswer->whereIn('user_id', array_unique($get_assign_student))->take($this->request->page*20)->get();

            }

        }



    }



    public function getSpeakingNonReviewed()

    {

        return SpeakingAnswer::with('review');

    }



    public function not_review_speaking()

    {



        $sql = SpeakingAnswer::with('speaking:id,title', 'user:id,first_name,last_name', 'review')->latest();



        $get_assign_student = TeacherEnroll::where('teacher_id', auth()->user()->id)->pluck('student_id')->toArray();

        $sql->whereIn('user_id', array_unique($get_assign_student));



//        return $sql->whereHas('review',function($q){

//            $q->where('user_id', '!=',auth()->id());

//        })->paginate(10);



        return $sql->paginate(10);

    }



    public function speaking_ungraded()

    {

        $final_data           = array();

        $get_company_students =

            User::where('role', 'student')->where('company_id', auth()->user()->company_id)->pluck('id');

        $speakings_ungraded   = SpeakingAnswer::with('speaking', 'user', 'teachers')

            ->whereNull('grade')

            ->withCount('teachers')

            ->orderBy('teachers_count', 'desc')

            ->whereIn('user_id', $get_company_students)->get();



        $index = 0;

        foreach ($speakings_ungraded as $key_main => $teacher) {

            foreach ($teacher->teachers as $key => $item) {

                if(Str::contains($item->lesson_mode, 'English')){

                    $teacher->teachers[$key]['teacher_name'] = DB::table('users')->where('id', $item->teacher_id)

                        ->select([

                            'users.first_name', 'users.last_name',

                            DB::raw("CONCAT(users.first_name, ' ', users.last_name) as full_name"),

                        ])->first();

                    $final_data[$index]['teacher']           = $teacher->teachers[$key]->teacher_name->full_name;



                    $final_data[$index]['title'] = $teacher->speaking->title;

                    $final_data[$index]['date']  = $teacher->created_at;



                    $final_data[$index]['student_info'] = $teacher->user->full_name;

                    $index++;
                }

            }

        }

        // sorting by teacher name

        $sort_data = array_column($final_data, 'teacher');

        array_multisort($final_data, SORT_DESC, $sort_data);

        return $final_data;



    }





}

