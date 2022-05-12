<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\TeacherDeposits;

class TeacherDepositsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return TeacherDeposits::where('teacher_id', $request->teacher_id)->orderBy('date', 'DESC')->get();
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
        $deposits = new TeacherDeposits();
        $deposits->fill($request->all());
       
        $deposits->save();
        return response()->json(['msg' => 'Deposits Added Successfully']);
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
        $deposits = TeacherDeposits::findOrFail($id);
        $deposits->fill($request->all());
        $deposits->save();
        return response()->json(['msg' => 'Deposits Updated Successfully']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deposits = TeacherDeposits::findOrFail($id);
        $deposits->delete();
        return response()->json(['msg' => 'Deposits has been deleted successfully']);
    }
}
