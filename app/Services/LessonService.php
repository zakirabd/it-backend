<?php


namespace App\Services;


use App\Course;
use App\Lesson;
use App\User;
use Illuminate\Support\Facades\DB;

class LessonService
{

    private $request;
    private $lesson;
    private $paginate;

    public function __construct($request, $paginate = 20)
    {
        $this->request = $request;
        $this->paginate = $paginate;
        $this->lesson = $this->getLessons();
    }

    /**
     * Filter by staff role
     *
     * @param $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getLessons()
    {

        $sql = Lesson::with('course');
        if ($this->request->keyword != '') {
            $sql->where('title', 'like', "%{$this->request->keyword}%")->orWhereHas('course', function ($course) {
                $course->where('title', 'like', "%{$this->request->keyword}%");
            });
        }
        if ($this->request->student_id) {
            $sql->WhereHas('course', function ($course) {
                $course->whereIn('id', User::findOrFail($this->request->student_id)->courses->pluck('id'));
            });
        }
        if ($this->request->course_id) {
            $sql->WhereHas('course', function ($course) {
                $course->where('id', "{$this->request->course_id}");
            });
        }
        return $sql->latest()->get();
    }
}
