<?php

namespace App\Services;
use Carbon\Carbon;
use App\SpeakingTeacherAttendance;

use App\TeacherEnroll;

use App\User;

class SpeakingTeacherAttendanceService {

    private $request;

    
    public function __construct($request)


    {


        $this->request = $request;

    }

    public function getTeacherStudentsAttendances(){
        $attendet_students = SpeakingTeacherAttendance::where('teacher_id', auth()->user()->id)

            ->whereDate('created_at', Carbon::today())
            ->whereDate('updated_at', Carbon::today())
            ->pluck('student_id');

        $enrolled_students = TeacherEnroll::where('teacher_id',  auth()->user()->id)->whereNotIn('student_id', $attendet_students)->pluck('student_id');

        return User::whereIn('id', $enrolled_students)->with(['studentEnrollClass:id,student_id,lesson_mode'])->take(20 * $this->request->page)->get();
       
    }


    public function getManagerData() {
        $final_data = [];
        $startdate = $this->request->start_date;

        $enddate = $this->request->end_date;

        $attendances = SpeakingTeacherAttendance::where('company_id', auth()->user()->company_id)

        ->whereBetween('created_at', [$startdate . " 00:00:00", $enddate . " 23:59:59"])
        ->orderBy('created_at', 'DESC')
        ->get();

        foreach($attendances as $item) {
            $user = [];

            $user['student'] = User::where('id', $item->student_id)->get()[0]->full_name;
            $user['teacher'] = User::where('id', $item->teacher_id)->get()[0]->full_name;
            $user['date'] = $item->created_at;

            array_push($final_data,  $user);
        }
        return $final_data;
    }
    
}