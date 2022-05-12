<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bonuses;

class BonusesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(auth()->user()->role == 'company_head' || auth()->user()->role == 'office_manager'){
            return Bonuses::where('company_id', auth()->user()->company_id)->where('date', $request->date)->orderBy('id', 'DESC')->get();
        }else if(auth()->user()->role == 'super_admin' || auth()->user()->role == 'accountant'){
             return Bonuses::where('company_id', $request->company_id)->where('date', $request->date)->orderBy('id', 'DESC')->get();
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
        $bonuses = new Bonuses();
        $bonuses->fill($request->all());
        if(auth()->user()->role == 'company_head' || auth()->user()->role == 'office_manager'){
            $bonuses->company_id = auth()->user()->company_id;
        }else if(auth()->user()->role == 'super_admin'){
            $bonuses->company_id = $request->company_id;
        }
        $bonuses->save();
        return response()->json(['msg' => 'Bonus added successfully.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Bonuses::findOrFail($id);
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
        $bonuses =  Bonuses::findOrFail($id);
        $bonuses->fill($request->all());
        $bonuses->save();
        return response()->json(['msg' => 'Bonus updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $bonuses =  Bonuses::findOrFail($id);
       
        $bonuses->delete();
        return response()->json(['msg' => 'Bonus has been deleted successfully.']);
    }
}
