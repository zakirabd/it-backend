<?php

namespace App\Http\Controllers;

use App\Essay;
use App\Helpers\UploadHelper;
use App\Http\Requests\EssayRequest;
use App\Lesson;
use App\Services\EssayService;
use Illuminate\Http\Request;

class EssayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $essays = (new EssayService($request))->getEssays();

        return response()->json($essays);
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
    public function store(EssayRequest $request)
    {

        $essay = new Essay();
        $essay->fill($request->all());
        if ($request->hasFile('essay_image')) {
            $essay->essay_image = UploadHelper::imageUploadOriginalSize($request->file('essay_image'), 'avatars');
        }
        $essay->save();
        return response()->json(['msg' => 'Essay created successfully.'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Essay  $essay
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(Essay::findOrFail($id), 200);
    }
    public function getlessonByCourse($id)
    {
        return response()->json(Lesson::where('course_id',$id)->get(), 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Essay  $essay
     * @return \Illuminate\Http\Response
     */
    public function edit(Essay $essay)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Essay  $essay
     * @return \Illuminate\Http\Response
     */
    public function update(EssayRequest $request, $id)
    {
        $essay = Essay::findOrFail($id);
        $essay->fill($request->all());
        if ($request->hasFile('essay_image')) {
            $essay->essay_image = UploadHelper::imageUploadOriginalSize($request->file('essay_image'), 'avatars');
        }
        $essay->save();
        return response()->json(['msg' => 'Essay Updated successfully.'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Essay  $essay
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $essay= Essay::findOrFail($id);
        $essay->delete();
        return response()->json(['msg' => 'Essay has been deleted successfully'], 200);
    }

}
