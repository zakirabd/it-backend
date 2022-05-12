<?php


namespace App\Services;


use App\Course;
use App\Lesson;
use App\StudentGroup;
use App\User;
use Illuminate\Support\Facades\DB;

class StudentGroupService
{

    private $request;
    private $group;
    private $paginate;

    public function __construct($request, $paginate = 20)
    {
        $this->request = $request;
        $this->paginate = $paginate;
        $this->group = $this->getGroups();
    }

    /**
     * Filter by staff role
     *
     * @param $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getGroups()
    {

        $sql = StudentGroup::query();
        if ($this->request->keyword != '') {
            $sql->where('title', 'like', "%{$this->request->keyword}%");
        }
        return $sql->take($this->request->page*20)->get();
    }
}
