<?php


namespace App\Services;


use App\Course;
use App\Essay;
use App\Lesson;
use App\Resource;
use App\Speaking;
use App\User;
use Illuminate\Support\Facades\DB;

class ResourceService
{

    private $request;
    private $resource;

    public function __construct($request)
    {
        $this->request = $request;
        $this->resource = $this->getResource();
    }

    /**
     * Get Resource
     *
     * @param $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getResource()
    {
        $sql = Resource::query()->select('id','title','status','attachment');

        if(auth()->user()->role !='super_admin'){
            $sql->whereHas('resource_relations', function ($query) {
                $query->where('role', auth()->user()->role);
            });
        }

        if ($this->request->keyword != '') {
            $sql->where('title', 'like', "%{$this->request->keyword}%");
        }
        return $sql->get();
    }
}
