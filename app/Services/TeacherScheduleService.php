<?php

namespace App\Services;


use App\TeacherSchedule;

use App\TeacherEnroll;

use App\User;

use App\StudentGroup;

class TeacherScheduleService {


    private $request;

    
    public function __construct($request)


    {


        $this->request = $request;

    }
// ////////////////////////////////////////////////////////////////////////////////////////////////////////

// //////////////TEACHER ROLE WEEKLY SCHEDULE ///////////////////////////////////////////////////////////
    public function getTeacherSchedule(){

        $teacher_id;
        $schedule;



        if(auth()->user()->role == 'teacher' || auth()->user()->role == 'head_teacher' || auth()->user()->role == 'speaking_teacher'){
            $teacher_id = auth()->user()->id;
        }else if(auth()->user()->role == 'office_manager' || auth()->user()->role == 'company_head'){
            $teacher_id = $this->request->query('teacher_id');
        }

        if($this->request->query('date') && $this->request->query('date') == 'weekly'){
            $schedule = TeacherSchedule::where('company_id', auth()->user()->company_id)->where('teacher_id', $teacher_id)->get();
        }else{
             $schedule = TeacherSchedule::where('company_id', auth()->user()->company_id)->where('teacher_id', $teacher_id)->where('weekday', $this->request->query('date'))->get();
        }
        
     
        
        foreach($schedule as $item){

            if($item->group_id == '9999' || $item->group_id == '999'){

                $user = User::where('id', $item->student_id)->get();

                $item->student =  $user;

                $item->group = 'One to One';


            }else if($item->group_id != '9999' || $item->group_id != '999'){

                $enroll = TeacherEnroll::where('teacher_id', $teacher_id)->where('student_group_id', $item->group_id)->pluck('student_id');
                
                $user = User::whereIn('id', $enroll)->get();

                $item->student = $user;

                $group = StudentGroup::where('id', $item->group_id)->get();

                $item->group = $group[0]->title;
            }
        }
        return $schedule;
    }


// ///////////////////////////////////////////////////////////////////////////////////////////

public function getAllTeacherSchedule(){

        // $schedule = TeacherSchedule::where('company_id', auth()->user()->company_id)->get();

 
        $teacherArray = [];
        $schedule = [];
        $teachers = User::whereIn('role', ['head_teacher', 'teacher', 'speaking_teacher'])->where('company_id', auth()->user()->company_id)->get();


       // $schedule = TeacherSchedule::where('company_id', auth()->user()->company_id)->whereIn('teacher_id', $teachers)->get();


        foreach ($teachers as $teacher) {

            $lockStatus = User::with('teacherLock')->findOrFail($teacher->id);

            array_push($teacherArray,  $lockStatus);

        }
        
        foreach($teacherArray as $data){
            $data->schedule = [];
            if($data->teacher_lock == null || $data->teacher_lock != null && $data->teacher_lock->lock_status == 0){
                    $teacherSchedule = TeacherSchedule::where('company_id', auth()->user()->company_id)->where('teacher_id', $data->id)->get();
                    foreach($teacherSchedule as $item){

                    if($item->group_id == '9999' || $item->group_id == '999'){

                        $user = User::where('id', $item->student_id)->get();

                        $item->student =  $user;

                        $item->group = 'One to One';

                        $item->teacher = User::where('id', $item->teacher_id)->get()[0]->full_name;

                    }else if($item->group_id != '9999' || $item->group_id != '999'){

                        $enroll = TeacherEnroll::where('teacher_id', $item->teacher_id)->where('student_group_id', $item->group_id)->pluck('student_id');
                        
                        $user = User::whereIn('id', $enroll)->get();

                        $item->student = $user;

                        $group = StudentGroup::where('id', $item->group_id)->get();

                        $item->group = $group[0]->title;

                        $item->teacher = User::where('id', $item->teacher_id)->get()[0]->full_name;
                    }
                    $data->schedule = $teacherSchedule;
                }
            }
        }

        return $teacherArray;
}
// /////////////////////////////////TEACHER ROLE DAILY SCHEDULE/////////////////////
    // public function getTeacherDailySchedule(){
        
        // $schedule = TeacherSchedule::where('company_id', auth()->user()->company_id)->where('teacher_id', auth()->user()->id)->where('weekday', $this->request->query('date'))->get();
     
        
        // foreach($schedule as $item){

        //     if($item->group_id == '9999'){

        //         $user = User::where('id', $item->student_id)->get();

        //         $item->student =  $user;

        //         $item->group = 'One to One';


        //     }else if($item->group_id != '9999'){

        //         $enroll = TeacherEnroll::where('teacher_id', auth()->user()->id)->where('student_group_id', $item->group_id)->pluck('student_id');
                
        //         $user = User::whereIn('id', $enroll)->get();

        //         $item->student = $user;

        //         $group = StudentGroup::where('id', $item->group_id)->get();

        //         $item->group = $group[0]->title;
        //     }
        // }
        // return $schedule;
    // }


    // //////////////////////////////////////////////////////////////////////////////////////////////
    // MANAGER ROLE GET TEACHER DAILY SCHEDULE //////////////////////////////////




    // /////////////////////////////////////////////////////////////////////////////////////////////
    // /////////////////////////MANAGER ROLE GET TEACHER WEEKLY SCHEDULE ////////////////////

    // public function getManagerTeacherWeeklySchedule(){

    //     $schedule = TeacherSchedule::where('company_id', auth()->user()->company_id)->where('teacher_id', $this->request->query('teacher_id'))->get();
     
        
    //     foreach($schedule as $item){

    //         if($item->group_id == '9999'){

    //             $user = User::where('id', $item->student_id)->get();

    //             $item->student =  $user;

    //             $item->group = 'One to One';


    //         }else if($item->group_id != '9999'){

    //             $enroll = TeacherEnroll::where('teacher_id', auth()->user()->id)->where('student_group_id', $item->group_id)->pluck('student_id');
                
    //             $user = User::whereIn('id', $enroll)->get();

    //             $item->student = $user;

    //             $group = StudentGroup::where('id', $item->group_id)->get();

    //             $item->group = $group[0]->title;
    //         }
    //     }
    //     return $schedule;
    // }

}

