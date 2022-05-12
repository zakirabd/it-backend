<?php

namespace App\Http\Controllers;

use App\CourseAssign;
use App\Http\Requests\CourseAssignRequest;
use App\Services\CourseAssignService;
use Illuminate\Http\Request;

class CourseAssignController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $getCourseAssign = (new CourseAssignService($request))->getCourseAssign();
        return response()->json($getCourseAssign);
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
    public function store(CourseAssignRequest $request)
    {
        $courseAssign = new CourseAssign();
        $courseAssign->fill($request->all());
        $courseAssign->save();
        return response()->json(['msg' => 'Course Assign successfully.'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CourseAssign  $courseAssign
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(CourseAssign::findOrFail($id), 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CourseAssign  $courseAssign
     * @return \Illuminate\Http\Response
     */
    public function edit(CourseAssign $courseAssign)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CourseAssign  $courseAssign
     * @return \Illuminate\Http\Response
     */
    public function update(CourseAssignRequest $request, $id){

        $courseAssign = CourseAssign::findOrFail($id);
        $courseAssign->fill($request->all());
        $courseAssign->save();
        return response()->json(['msg' => 'Course Assign Updated successfully.'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CourseAssign  $courseAssign
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $courseAssign = CourseAssign::findOrFail($id);
        $courseAssign->delete();
        return response()->json(['msg' => 'Course Assign has been deleted successfully'], 200);
    }
}
