<?php

namespace App\Services;

use App\Advanced;
use App\Course;
use App\Lesson;
use App\Listening;
use App\StudentGroup;
use App\TeacherEnroll;
use App\User;
use Illuminate\Support\Facades\DB;

class ListeningService
{
    private $request;
    private $listening;
    private $paginate;

    public function __construct($request, $paginate = 20)
    {
        $this->request = $request;
        $this->paginate = $paginate;
        $this->listening = $this->getParentlistening();
    }

    /**
     * Filter by staff role
     *
     * @param $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getParentlistening()
    {
        $course_ids = array();
        $get_parent_child_ids = User::with('children')->find($this->request->parent_id)->children->pluck('id');
        foreach ($get_parent_child_ids as $key => $item) {
            $get_courses = User::with('courses')->find($item)->courses->pluck('id');
            if (!empty($get_courses)){
                foreach ($get_courses as $get_cours_id) {
                    $course_ids[] = $get_cours_id;
                }
            }
        }
        $get_liseting = Listening::whereIn('course_id',array_unique($course_ids))->latest();
        return $get_liseting->paginate(2);
    }
}
