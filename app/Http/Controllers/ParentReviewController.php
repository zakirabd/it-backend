<?php

namespace App\Http\Controllers;

use App\Http\Requests\ParentReviewRequest;
use App\ParentReview;
use App\Services\ParentReviewService;
use Illuminate\Http\Request;

class ParentReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if(auth()->user()->role == 'parent'){
            $parentreview = (new ParentReviewService($request))->getParentReview();
        } else if(auth()->user()->role == 'teacher' || auth()->user()->role == 'head_teacher'){

            $parentreview = (new ParentReviewService($request))->getparentreviewforteacher();
        }

        return response()->json($parentreview);
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
    public function store(ParentReviewRequest $request)
    {
        $parentreview = new ParentReview();
        $parentreview->fill($request->all());
        $parentreview->save();

        return response()->json(['msg' => 'Parent Review  created successfully.'], 200);


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $teacherreview = ParentReview::with('comment')->findorFail($id);
        return response()->json($teacherreview);
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
    public function update(ParentReviewRequest $request, $id)
    {
//        return $request->all();

         $parentreview = ParentReview::findorfail($id);

        $parentreview->fill($request->all());
        $parentreview->save();

        return response()->json(['msg' => 'Parent Review  updated successfully.'], 200);


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $parentReview = ParentReview::find($id);

        $parentReview->delete();

        return response()->json(['msg' => 'Parent Review  has been deleted successfully.']);

    }
}
