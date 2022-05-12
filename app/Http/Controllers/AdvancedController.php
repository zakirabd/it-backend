<?php

namespace App\Http\Controllers;

use App\Advanced;
use App\Http\Requests\AdvancedRequest;
use App\Services\AdvancedService;
use Illuminate\Http\Request;

class AdvancedController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $certificate = (new AdvancedService($request))->getAdvanced();
        return response()->json($certificate);
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
    public function store(AdvancedRequest $request)
    {
        $advanced= new Advanced();
        $advanced->fill($request->all());
        $advanced->save();
        return response()->json(['msg' => 'Advanced created successfully.'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Advanced  $advanced
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(Advanced::findOrFail($id), 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Advanced  $advanced
     * @return \Illuminate\Http\Response
     */
    public function edit(Advanced $advanced)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Advanced  $advanced
     * @return \Illuminate\Http\Response
     */
    public function update(AdvancedRequest $request, $id)
    {
        $advanced = Advanced::findOrFail($id);
        $advanced->fill($request->all());
        $advanced->save();
        return response()->json(['msg' => 'Advanced Updated successfully.'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Advanced  $advanced
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $advanced = Advanced::findOrFail($id);
        $advanced->delete();
        return response()->json(['msg' => 'Advanced has been deleted successfully'], 200);

    }
}
