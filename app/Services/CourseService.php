<?php



namespace App\Services;



use App\Company;

use App\Course;

use App\CourseAssign;

use App\User;

use Illuminate\Support\Facades\DB;



class CourseService

{

    private $request;

    private $course;



    public function __construct($request)

    {

        $this->request = $request;

        $this->course = $this->getCourses();

    }



    /**

     * Filter by staff role

     *

     * @param $request

     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection

     */

    public function getCourses()

    {



         $user = auth()->user();

         $sql = Course::query();



         $company_group = ['company_head','office_manager','teacher','head_teacher','speaking_teacher'];

         $manager_group = ['super_admin','content_manager','content_master'];



        if ($this->request->student_assign_course == 1) {

            $sql->whereIn('id', User::findOrFail($this->request->student_id)->courses->pluck('id'));

            return $sql->latest()->get();

        }



        if (in_array($user->role,$manager_group)) {

            return $sql->latest()->get();

        }

        if ($user->role == 'parent') {

            $sql->whereIn('id', User::findOrFail($user->id)->children->map->courses->collapse()->pluck('id'));

            return $sql->latest()->get();

        }

        if (in_array($user->role,$company_group)) {

            $sql->whereIn('id', Company::findOrFail($user->company_id)->courses->pluck('course_id'));

            return $sql->latest()->get();

        }

        if ($user->role == 'student') {

            $sql->whereIn('id', User::findOrFail($user->id)->courses->pluck('id'));

            return $sql->get();



            if ($this->request->page == 1) {

                $sql->whereIn('id', User::findOrFail($user->id)->courses->pluck('id'));

                return $sql->get();

            } else {

                $sql->whereIn('id', User::findOrFail($user->id)->courses->pluck('id'));

                return $sql->latest()->get();

            }

        }

    }



    public function getCoursesNotCompany($id){



        $sql = Course::query();



        $sql->whereNotIn('id', Company::findOrFail($id)->courses->pluck('course_id'));

        return $sql->latest()->get();

  

    }

}

