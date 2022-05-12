<?php

namespace App\Http\Controllers;

use App\Expenditure;
use App\Http\Requests\TeacherAttendanceRequest;
use App\ToDo;
use Illuminate\Http\Request;
use App\TeacherPayment;

class TeacherAttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $TeacherPayment= TeacherPayment::with('teacher')->where('teacher_id',$request->teacher_id)->paginate(10);

        return response()->json($TeacherPayment);
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
    public function store(TeacherAttendanceRequest $request)
    {
        TeacherPayment::create([
            'class_type' => $request->class_type,
            'status' => $request->status,
            'number_of_class' => (int)$request->number_of_class,
            'teacher_id' => (int)$request->teacher_id,
            'company_id' => (int)$request->company_id,
        ]);

        return response()->json(['msg' => 'TeacherPayment  created successfully.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(TeacherPayment::findOrFail($id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TeacherAttendanceRequest $request, $id)
    {
//        return $request->all();
//        return $id;

        TeacherPayment::where('id',$id)->update([
            'class_type' => $request->class_type,
            'status' => $request->status,
            'number_of_class' => (int)$request->number_of_class,
            'teacher_id' => (int)$request->teacher_id,
            'company_id' => (int)$request->company_id,
        ]);

        return response()->json(['msg' => 'TeacherPayment  Updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        TeacherPayment::destroy($id);

        return response()->json(['msg' => 'Expenditure deleted successfully.']);
    }
}
