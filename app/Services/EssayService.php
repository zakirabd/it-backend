<?php


namespace App\Services;


use App\Course;
use App\Essay;
use App\Lesson;
use App\User;
use Illuminate\Support\Facades\DB;

class EssayService
{

    private $request;
    private $essay;

    public function __construct($request)
    {
        $this->request = $request;
        $this->essay = $this->getEssays();
    }

    /**
     * Filter by staff role
     *
     * @param $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getEssays()
    {
        $sql = Essay::with('course', 'lesson')->latest();
        if ($this->request->keyword != '') {
            $sql->where('title', 'like', "%{$this->request->keyword}%")
                ->orWhere('essay_type', 'like', "%{$this->request->keyword}%")
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
}
