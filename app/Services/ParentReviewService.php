<?php


namespace App\Services;

use     App\ParentReview;
use App\User;
use Illuminate\Support\Facades\DB;

class ParentReviewService
{

    private $request;
    private $parentreview;

    public function __construct($request)
    {
        $this->request = $request;
        $this->parentreview = $this->getParentReview();
    }

    /**
     * Filter by staff role
     *
     * @param $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getParentReview()
    {
        $teachers_ids = array();
        $user = User::with('children.teachers')->findOrFail(auth()->id());
        foreach ($user->children as $key => $children) {
            foreach ($children->teachers as $key => $teacher) {
                $teachers_ids[] = $teacher->teacher_id;
            }
        }
        // get all teacher assign student
        $teachers_ids = array_unique($teachers_ids);
        $sql = ParentReview::with('teacher')
            ->withCount('comment')
            ->where('user_id', auth()->user()->id)
            ->whereIn('teacher_id', $teachers_ids)->latest();

        // Data search for Parent Review
        if ($this->request->keyword != '') {
            $sql->where('note', 'like', "%{$this->request->keyword}%")
                ->orWhereHas('teacher', function ($q) {
                    $q->where(DB::raw("concat(first_name,' ',last_name)"), 'like', "%{$this->request->keyword}%");
                });
        }

        return $sql->paginate(10);
    }


    public function getparentreviewforteacher()
    {
        $sql = ParentReview::with('teacher','parent')->withCount('comment')->where('teacher_id', auth()->user()->id)->latest();

        if ($this->request->keyword != '') {
            $sql->where('note', 'like', "%{$this->request->keyword}%")
                ->orWhereHas('parent', function ($q) {
                    $q->where(DB::raw("concat(first_name,' ',last_name)"), 'like', "%{$this->request->keyword}%");
                });
        }
        return $sql->paginate(10);
    }
}
