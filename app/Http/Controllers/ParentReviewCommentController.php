<?php

namespace App\Http\Controllers;

use App\ParentReviewComment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use auth;

class ParentReviewCommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
//        return $request->all();

        ParentReviewComment::insert([
            'comment'=>$request->comment,
            'parent_review_id'=>$request->id,
            'user_id'=>auth()->user()->id,
            'created_at'=>Carbon::now()

        ]);
        return response()->json(['msg' => 'Comment added successfully.'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ParentReviewComment  $parentReviewComment
     * @return \Illuminate\Http\Response
     */
    public function show(ParentReviewComment $parentReviewComment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ParentReviewComment  $parentReviewComment
     * @return \Illuminate\Http\Response
     */
    public function edit(ParentReviewComment $parentReviewComment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ParentReviewComment  $parentReviewComment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ParentReviewComment $parentReviewComment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ParentReviewComment  $parentReviewComment
     * @return \Illuminate\Http\Response
     */
    public function destroy(ParentReviewComment $parentReviewComment)
    {
        //
    }
}
