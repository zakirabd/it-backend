<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TeacherBonus;

class TeacherBonusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return TeacherBonus::where('teacher_id', $request->teacher_id)->orderBy('date', 'DESC')->get();
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
        $bonus = new TeacherBonus();
        $bonus->fill($request->all());
       
        $bonus->save();
        return response()->json(['msg' => 'Bonus Added Successfully']);
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
        $bonus = TeacherBonus::findOrFail($id);
        $bonus->fill($request->all());
        $bonus->save();
        return response()->json(['msg' => 'Bonus Updated Successfully']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(auth()->user()->role == 'super_admin'){
            $bonus = TeacherBonus::findOrFail($id);
            $bonus->delete();
            return response()->json(['msg' => 'Bonus has been deleted successfully']);
        }
        
    }
}
