<?php



namespace App\Services;



use App\EssayAnswer;

use App\EssayReview;

use App\TeacherEnroll;

use App\User;

use Illuminate\Support\Facades\DB;



class EssayAnswerService

{

    private $request;

    private $essayAnswer;



    public function __construct($request)

    {

        $this->request     = $request;

        $this->essayAnswer = $this->essayAnswers()->with('plagiarism');

    }



    /**

     * Base filter of essayAnswers

     *

     * @param $request

     * @return \Illuminate\Database\Eloquent\Builder

     */

    private function essayAnswers()

    {



        $essayAnswer = EssayAnswer::with('essay', 'user', 'reviews.user', 'reviews')->orderBy('submit_date', 'DESC');



        if ($this->request->keyword != '') {

            $essayAnswer->where(function ($query) {

                $query->whereHas('user', function ($q) {

                    $q->where(DB::raw("concat(first_name,' ',last_name)"), 'like', "%{$this->request->keyword}%")

                        ->orWhere('email', 'like', "%{$this->request->keyword}%");

                })

                    ->orWhereHas('essay', function ($q) {

                        $q->where('title', 'like', "%{$this->request->keyword}%")

                            ->orwhere('essay_type', 'like', "%{$this->request->keyword}%")

                            ->orwhere('created_at', 'like', "%{$this->request->keyword}%")

                            ->orWhere('question', 'like', "%{$this->request->keyword}%");

                    });

            });

        }

        return $essayAnswer;

    }



    /** get essay list

     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator

     */

    public function getEssaysList()

    {

        $office_group = ['company_head', 'office_manager'];

        $teacher_group = ['teacher', 'head_teacher'];

        $user         = auth()->user();

        $sql          = $this->essayAnswer->where('is_submitted', 1);



        if ($this->request->student_list == 1) {

            $sql->where('user_id', $this->request->student_id);

        } else {

            if (in_array($user->role, $teacher_group))  {

                $get_assign_student = TeacherEnroll::where('teacher_id', $user->id)->where('status', '1')->pluck('student_id')->toArray();

                $sql->whereIn('user_id', array_unique($get_assign_student));

            }

            if (in_array($user->role, $office_group)) {

                $company_students = User::CompanyAllStudent($user->company_id)->pluck('id');

                $sql->where(function ($q) use ($company_students) {

                    $q->whereIn('user_id', $company_students);

                });

            }

        }



        return $sql->take($this->request->page*20)->get();

    }





    /** Get Celt Essays for head teacher.

     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator

     */

    public function celtEssays()

    {

        $essays = $this->essayAnswer->where(function ($q) {

            $q->whereIn('user_id', User::where('company_id', auth()->user()->company_id)->pluck('id'));

        })->where('is_submitted', 1)
        
            ->whereHas('essay', function ($q) {
                if($this->request->essay_type == 'midterm_end_course'){
                    $q->where('essay_type', 'midterm_end_course');
                } else if($this->request->essay_type == 'unit') {
                    $q->where('essay_type', 'unit');
                }
            })->take($this->request->page*20)->get();



        foreach ($essays as $value) {

            $teacher_enroll_id     = TeacherEnroll::where('student_id', $value->user->id)->pluck('teacher_id');

            $value->user->teachers = DB::table('users')

                ->whereIn('id', $teacher_enroll_id)

                ->select('first_name', 'last_name', DB::raw("CONCAT(users.first_name,' ',users.last_name) as full_name"))

                ->get();

        }

        return $essays;

    }





    /**

     * get non review Celt Essay for head teacher role

     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator

     */

    public function NonReviewCeltEassay()

    {

        // return EssayAnswer::with('essay', 'user', 'reviews.user', 'reviews')->where(function ($q) {

        //     $q->whereIn('user_id', User::where('company_id', auth()->user()->company_id)->pluck('id'));

        // })->where('is_submitted', 1)->whereHas('essay', function ($q) {

        //     $q->where('essay_type', 'midterm_end_course');

        // })->latest('id')->paginate(10);

        return EssayAnswer::with('essay', 'user', 'reviews.user', 'reviews')->where(function ($q) {
            $q->whereIn('user_id', User::where('company_id', auth()->user()->company_id)->pluck('id'));
        })->where('grade','!=', null)->where('is_closed', 0)->whereHas('essay', function ($q) {
            $q->where('essay_type', 'midterm_end_course');
        })->latest('id')->get();



    }



    /**

     * Get closed essay answers

     *

     * @param $request

     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator

     */

    public function closedAnswers()

    {

        return $this->essayAnswer

            ->where('user_id', $this->request->user_id)

            ->where('is_closed', 1)

            ->paginate(10);

    }



    /**

     * Get all essay answers

     * @param $request

     * @return int

     */

    public function reviews()

    {



        return EssayReview::whereIn('essay_answer_id', auth()->user()->answers->pluck('id'))

            ->where('is_student', 0)

            ->whereNull('seen_at')

            ->count();

    }



    /**

     * Filter by teacher or company

     *

     * @param $request

     * @return int

     */

    public function nonReviewedAnswers()

    {

        return $this->essayAnswer->where(function ($q) {

            if (auth()->user()->role == 'company_head') {

                $q->whereIn('user_id', User::where('company_id', auth()->user()->company_id)->pluck('id'));

            } else {

                $q->whereIn('user_id', auth()->user()->students->pluck('student_id'));

            }

        })

            ->whereHas('essay', function ($q) {

                if (auth()->user()->role == 'company_head') {

                    $q->where('essay_type', 'midterm_end_course');

                } else {

                    $q->where('essay_type', 'unit');

                }

            })

            ->doesntHave('reviews')

            ->count();

    }



    /**

     * get essay list teacher and head teacher dashboard

     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator

     */

    public function EssayNotReview()

    {



        $EssayAnswer = EssayAnswer::with('essay:id,title', 'user', 'reviews.user', 'reviews')

            ->latest('id');



        if (auth()->user()->role == 'head_teacher') {

            return $data = $EssayAnswer->where(function ($q) {

                $q->whereIn('user_id', auth()->user()->students->where('status', '1')->pluck('student_id'));

            })->where('is_submitted', 1)->whereHas('essay', function ($q) {

                $q->where('essay_type', 'unit');

            })->paginate(10);



        } else if (auth()->user()->role == 'teacher') {

            return $data = $EssayAnswer->where(function ($q) {

                $q->whereIn('user_id', auth()->user()->students->where('status', '1')->pluck('student_id'));

            })->where('is_submitted', 1)->paginate(10);



        }



    }

}

