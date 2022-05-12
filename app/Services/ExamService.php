<?php

namespace App\Services;
use App\Exam;
use App\Examlocked;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExamService
{

    private   $request;
    protected $pagination = 20;

    public function __construct($request)
    {
        $this->request = $request;

    }

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getExams()
    {

        $sql = Exam::with('course', 'lesson');

        if ($this->request->keyword != '') {
            $sql->where('title', 'like', "%{$this->request->keyword}%")
                ->orWhere('description', 'like', "%{$this->request->keyword}%")
                ->orWhereHas('course', function ($course) {
                    $course->where('title', 'like', "%{$this->request->keyword}%");
                })->orWhereHas('lesson', function ($lesson) {
                    $lesson->where('title', 'like', "%{$this->request->keyword}%");
                });
        }

        return $sql->latest()->take($this->request->page*20)->get();
    }

    /** Unlock exam list
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getLockUnlockExams()
    {
        $course_id    = $this->request->course_id;
        $lesson_id    = $this->request->lesson_id;
        $student_id   = $this->request->student_id;
        $studentExams = Exam::where('course_id', $course_id)->where('lesson_id', $lesson_id)->get();
        foreach ($studentExams as $exam) {
            $examblock = Examlocked::where('course_id', $course_id)
                ->where('lesson_id', $lesson_id)
                ->where('student_id', $student_id)
                ->where('exam_id', $exam->id)
                ->first();
            if (empty($examblock)) {
                $examlocked             = new Examlocked();
                $examlocked->exam_id    = $exam->id;
                $examlocked->lesson_id  = $lesson_id;
                $examlocked->student_id = $student_id;
                $examlocked->course_id  = $course_id;
                $examlocked->save();
            }
        }
        $sql = Examlocked::with('exam')->where('course_id', $course_id)
            ->where('lesson_id', $lesson_id)
            ->where('student_id', $student_id);
        if ($this->request->home_work == 0) {
            $sql->WhereHas('exam', function ($exam) {
                $exam->where('retake_minutes', '!=', 0);
                $exam->where('retake_time', '!=', 0);
            });
        }
        if ($this->request->keyword != '') {
            $sql->where('title', 'like', "%{$this->request->keyword}%");
        }
        return $sql->latest()->get();

    }

    /** get student role exam or homework list
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getStudentExams()
    {

        $nowStr     = date("Y-m-d H:i:s");
        $student_id = $this->request->student_id;
        $sql        = Examlocked::with('course:id,title','exam')->where('student_id', $student_id);
        if ($this->request->has('home_work')) {

            $sql->whereHas('exam', function ($exam) {
                $exam->where('retake_minutes', 0);
                $exam->where('retake_time', 0);
            });
        } else {

            $sql->whereHas('exam', function ($exam) {
                $exam->where([
                    ['retake_minutes', '!=', 0],
                    ['retake_time', '!=', 0],
                ]);
            });
        }
        if ($this->request->keyword != '') {
            $sql->whereHas('exam', function ($exam) {
                $exam->where('title', "%{$this->request->keyword}%");
            });
        }
        if ($this->request->student_id) {
            $sql->WhereHas('course', function ($course) {
                $course->whereIn('id', User::findOrFail($this->request->student_id)->courses->pluck('id'));
            });
        }

        $sql->where('is_block', 1);

        $exams = $sql->latest('id')->get();

        foreach ($exams as $item) {
            if ($this->request->home_work != 1) {
                DB::table('exam_locked')
                    ->where('id', $item->id)
                    ->whereRaw('DATE_ADD(updated_at, INTERVAL "' . $item->exam->duration_minutes . '" MINUTE) <= "'
                               . $nowStr . '"')
                    ->update(['is_block' => 0]);
            }
            if (Carbon::parse($item->updated_at)->addMinute($item->exam->duration_minutes)->format('Y-m-d H:i:s')
                <= $nowStr) {
                $item->is_block = 0;
            }
        }
        return $exams;
    }

    /** Unlock Home Work list
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getlockUnlockHomeWork()
    {
        $course_id    = $this->request->course_id;
        $lesson_id    = $this->request->lesson_id;
        $student_id   = $this->request->student_id;
        $studentExams = Exam::where('course_id', $course_id)
            ->where('lesson_id', $lesson_id)
            ->where('retake_minutes', 0)
            ->where('retake_time', 0)
            ->get();
        foreach ($studentExams as $exam) {
            $examblock = Examlocked::where('course_id', $course_id)
                ->where('lesson_id', $lesson_id)
                ->where('student_id', $student_id)
                ->where('exam_id', $exam->id)
                ->first();
            if (empty($examblock)) {
                $examlocked             = new Examlocked();
                $examlocked->exam_id    = $exam->id;
                $examlocked->lesson_id  = $lesson_id;
                $examlocked->student_id = $student_id;
                $examlocked->course_id  = $course_id;
                $examlocked->save();
            }
        }
        $sql = Examlocked::with('exam')->where('course_id', $course_id)
            ->where('lesson_id', $lesson_id)
            ->where('student_id', $student_id);
        $sql->whereHas('exam', function ($exam) {
            $exam->where('retake_minutes', 0);
            $exam->where('retake_time', 0);
        });
        return $sql->latest()->get();

    }

}
