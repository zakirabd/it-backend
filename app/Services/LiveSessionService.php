<?php


namespace App\Services;


use App\Advanced;
use App\Course;
use App\Lesson;
use App\Listening;
use App\LiveSession;
use App\StudentGroup;
use App\TeacherEnroll;
use App\User;
use Illuminate\Support\Facades\DB;

class LiveSessionService
{

    private $request;
    private $liveSession;
    private $paginate;

    public function __construct($request, $paginate = 20)
    {
        $this->request = $request;
        $this->paginate = $paginate;
        $this->liveSession = $this->getLiveSession();
    }

    /**
     * Filter by staff role
     *
     * @param $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getWeekDay($item)
    {
        switch ($item) {
            case "Sunday":
                $item = "0";
                break;
            case "Monday":
                $item = "1";
                break;
            case "Tuesday":
                $item = "2";
                break;
            case "Wednesday":
                $item = "3";
                break;
            case "Thursday":
                $item = "4";
                break;
            case "Friday":
                $item = "5";
                break;
            default:
                $item = "6";
        }
        return $item;

    }

    public function getLiveSession()
    {

        $sql = LiveSession::with('group')->where('user_id', $this->request->user_id);
        if ($this->request->keyword != '') {
            $sql->where('study_mode', 'like', "%{$this->request->keyword}%");
                $sql->orWhere('start_time', 'like', "%{$this->request->keyword}%");
                $sql->orWhere('week_day', 'like', "%{$this->getWeekDay($this->request->keyword)}%");
        }
        return $sql->paginate(20);
    }
}
