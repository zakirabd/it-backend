<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\TeacherSchedule;

use App\Http\Requests\TeacherScheduleRequests;

use Illuminate\Support\Facades\DB;
use App\Services\TeacherScheduleService;

class TeacherScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->query('type') && $request->query('type') == 'all'){
            return (new TeacherScheduleService($request))->getAllTeacherSchedule();
        }else{
            return (new TeacherScheduleService($request))->getTeacherSchedule();
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
       $check_exists;
        $params = json_decode($request->params, true);

        if($params[0]['group_id'] == 9999){
            $check_exists = TeacherSchedule::where('teacher_id', $params[0]['teacher_id'])->where('student_id', $params[0]['student_id'])->get();

        }else{
             $check_exists = TeacherSchedule::where('teacher_id', $params[0]['teacher_id'])->where('group_id', $params[0]['group_id'])->get();
        }

        foreach($check_exists as $key => $item){
            $sql = TeacherSchedule::findOrFail($item->id);
            $sql->delete();
        }

       
        foreach($params  as $key => $item){
            
            $schedule = new TeacherSchedule();
            $schedule->fill($item);
            $schedule->company_id = auth()->user()->company_id;
            $schedule->save();
           
        }
        return response()->json(['msg' => 'Schedule created Successfully']);
        // return $params;
       
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
