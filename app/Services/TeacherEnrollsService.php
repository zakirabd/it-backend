<?php





namespace App\Services;





use App\Course;

use App\Lesson;

use App\StudentGroup;

use App\TeacherEnroll;

use App\User;

use Illuminate\Support\Facades\DB;

use App\TeacherSchedule;





class TeacherEnrollsService

{



    private $request;

    private $group;

    private $paginate;



    public function __construct($request, $paginate = 20)

    {

        $this->request = $request;

        $this->paginate = $paginate;

        $this->group = $this->getEnrolls();

    }



    /**

     * Filter by staff role

     *

     * @param $request

     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator

     */

     public function getTeacherStudents(){
        $sql = TeacherEnroll::with('teacher')->where('teacher_id', $this->request->teacher_id)->get();

        foreach($sql as $enroll){
            $student = User::where('id', $enroll->student_id)->get();
            $group_name = StudentGroup::where('id', $enroll->student_group_id)->select('title')->get();
            if(count($student) != 0){
                $enroll->student = $student[0]->full_name;
            }
            

            if(count($group_name) != 0){
                $enroll->group = $group_name[0]->title;
            }else if(count($group_name) == 0){
                $enroll->group = 'One to One';
            }
            if($enroll->student_group_id == '9999'){
                $schedule = TeacherSchedule::where('teacher_id', $this->request->teacher_id)->where('student_id', $enroll->student_id)->where('group_id', '9999')->get();
                $enroll->schedule = $schedule;  
            }else if($enroll->student_group_id != '9999'){
                $schedule = TeacherSchedule::where('teacher_id', $this->request->teacher_id)->where('group_id', $enroll->student_group_id)->where('student_id', null)->get();
                $enroll->schedule = $schedule; 
            }
            
        }
        return  $sql;
    }

    public function getEnrolls()

    {



        $sql = TeacherEnroll::with('teacher')->where('student_id',$this->request->student_id);

        if ($this->request->keyword != '') {

            $sql->where('lesson_mode', 'like', "%{$this->request->keyword}%")

                ->orWhere('study_mode', 'like', "%{$this->request->keyword}%")

                ->orWhere('fee', 'like', "%{$this->request->keyword}%")

                ->orWhereHas('teacher', function ($teacher) {

                    $teacher->where(DB::raw("concat(first_name,' ',last_name)"), 'like', "%{$this->request->keyword}%");

                });

        }

        return $sql->get();

    }

}

