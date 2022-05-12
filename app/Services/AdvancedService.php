<?php

namespace App\Services;
use App\Advanced;
class AdvancedService
{

    private $paginate;

    public function __construct($request)
    {
        $this->request = $request;
        $this->group = $this->getAdvanced();
    }

    /**
     * Filter by staff role
     *
     * @param $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAdvanced()
    {

        $sql = Advanced::where('student_id',$this->request->student_id)->select('id','course_type','score','certificate');

        if ($this->request->keyword != '') {
            $sql->where('course_type', 'like', "%{$this->request->keyword}%")
                ->orWhere('score', 'like', "%{$this->request->keyword}%");
        }
        return $sql->get();
    }
}
