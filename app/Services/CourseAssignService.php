<?php


namespace App\Services;


use App\Course;
use App\CourseAssign;
use App\Essay;
use App\Lesson;
use App\User;
use Illuminate\Support\Facades\DB;

class CourseAssignService
{

    private $request;
    private $courseAssign;

    public function __construct($request)
    {
        $this->request = $request;
        $this->courseAssign = $this->getCourseAssign();
    }

    /**
     * Filter by staff role
     *
     * @param $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getCourseAssign()
    {
        $sql = CourseAssign::with('course', 'company');
        $keyword = $this->request->keyword;
        $sql->where(function ($query) use ($keyword) {
            if ($keyword != '') {
                $query->whereHas('company', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
                $query->orWhereHas('course', function ($q) use ($keyword) {
                    $q->where('title', 'like', "%{$keyword}%");
                });

            }
        }
        );
        return $sql->take($this->request->page*20)->get();
    }
}
