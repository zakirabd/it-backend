<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\OfficeManagerBonus;
use App\User;

class OfficeManagerBonusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(auth()->user()->role == 'company_head' || auth()->user()->role == 'super_admin' || auth()->user()->role == 'content_master'){
           
           return OfficeManagerBonus::where('office_manager_id', $request->office_manager_id)->orderBy('date', 'DESC')->get();

        }else if(auth()->user()->role == 'accountant'){
            
           

            $bonus =  OfficeManagerBonus::where('company_id', $request->company_id)
            ->where('date', $request->date)
            ->orderBy('date', 'DESC')->get();

            $final_data = [];

            foreach($bonus as $item){
               $user = User::where('id',  $item->office_manager_id)->first();
               if(isset($user)){
                    $item->user = $user;
                    array_push($final_data, $item);
               }
              
            }
            return $final_data;
            // return response()->json(['com' => $request->company_id, 'date' => $request->date]);
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
        $bonus = new OfficeManagerBonus();
        $bonus->fill($request->all());
        $bonus->company_id = auth()->user()->company_id;
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
        $bonus = OfficeManagerBonus::findOrFail($id);
        $bonus->fill($request->all());
         $bonus->company_id = auth()->user()->company_id;
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
            $bonus = OfficeManagerBonus::findOrFail($id);
            $bonus->delete();
            return response()->json(['msg' => 'Bonus has been deleted successfully']);
        }
    }
}
