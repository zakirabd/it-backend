<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\SpeakingTeacherAttendanceRequests;

use App\SpeakingTeacherAttendance;

use App\TeacherEnroll;

use App\User;


use App\Services\SpeakingTeacherAttendanceService;

use Carbon\Carbon;

class SpeakingTeacherAttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(auth()->user()->role == "speaking_teacher"){

           return (new SpeakingTeacherAttendanceService($request))->getTeacherStudentsAttendances(); 

        }else if(auth()->user()->role == "company_head" || auth()->user()->role == "office_manager"){

            return (new SpeakingTeacherAttendanceService($request))->getManagerData();

        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $form = new SpeakingTeacherAttendance();

        $form->fill($request->all());

        $form->save();

        return response()->json(['msg' => 'Attendance Added Successfully']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
