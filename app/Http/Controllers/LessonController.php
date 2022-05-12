<?php

namespace App\Http\Controllers;

use App\Course;
use App\Http\Requests\LessonRequest;
use App\Lesson;
use App\Services\LessonService;
use Illuminate\Http\Request;
use App\Helpers\UploadHelper;
use Illuminate\Support\Facades\Storage;

class LessonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $lessons = (new LessonService($request))->getLessons();
        return response()->json($lessons);
    }

    public function lessonCourses()
    {
       $courses=Course::get();
        return response()->json($courses);

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
    public function store(LessonRequest $request)
    {   
        $data = $request->except('audio_file','video_file');

        if ($request->hasFile('audio_file')) {
            $data['audio_file'] = UploadHelper::fileUpload($request->file('audio_file'), 'audios');
        }

        if ($request->hasFile('video_file')) {
            $data['video_file'] = UploadHelper::fileUpload($request->file('video_file'), 'videos');
        }

        $lesson = new Lesson();
        $lesson->fill($data);
        $lesson->save();
        return response()->json(['msg' => 'Lesson created successfully.'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Lesson  $lesson
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(Lesson::findOrFail($id), 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Lesson  $lesson
     * @return \Illuminate\Http\Response
     */
    public function edit(Lesson $lesson)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Lesson  $lesson
     * @return \Illuminate\Http\Response
     */
    public function update(LessonRequest $request, $id)
    {
        
        $lesson = Lesson::findOrFail($id);
        
        $data = $request->except('audio_file','video_link','video_file');

        if ($request->hasFile('audio_file')) {
            if ($lesson->audio_file) {
                Storage::delete('public/' . $lesson->audio_file);
            }
            $data['audio_file'] = UploadHelper::fileUpload($request->file('audio_file'), 'audios');
        }

        if ($request->hasFile('video_file')) {
            
            if($lesson->video_file != null){  
                if ($lesson->video_file) {
                    Storage::delete('public/' . $lesson->video_file);
                }
                $data['video_file'] = UploadHelper::fileUpload($request->file('video_file'), 'videos');
                $data['video_link'] = null;
            }else{
                
                // if ($lesson->video_file) {
                //     Storage::delete('public/' . $lesson->video_file);
                // }
                $data['video_file'] = UploadHelper::fileUpload($request->file('video_file'), 'videos');
                $data['video_link'] = null;
            }  
        }else{
            $data['video_file'] = null;
            $data['video_link'] = $request->video_link;
        }

        // if($lesson->video_link == null || $request->video_link!= null){
        //     $data['video_file'] = null;
        //     $data['video_link'] = $request->video_link;
        // }

        

        $lesson->fill($data);
        $lesson->save();
        return response()->json(['msg' => 'Lesson Updated successfully.'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Lesson  $lesson
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $lesson = Lesson::findOrFail($id);
        
        if($lesson->video_file != null){
            Storage::delete('public/' . $lesson->video_file);
        }

        if ($lesson->audio_file != null) {
            Storage::delete('public/' . $lesson->audio_file);
        }

        $lesson->delete();
        return response()->json(['msg' => 'Lesson has been deleted successfully'], 200);

    }

    public function getAllLesson()
    {
        $lesson = Lesson::get();
        return response()->json($lesson);

    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function essays(Request $request, $id)
    {

        $essays = Lesson::findOrFail($id)->essays()->with(['answers'=>function($answer){
            $answer->where('user_id',auth()->user()->id);

        },'latestAnswer' => function($q) {
                $q->with(['latestReview' => function($query) {
                    $query->where('is_student', 0);
                }])
                    ->where('user_id', auth()->user()->id);
            }])
            ->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->keyword}%")
                    ->orwhere('essay_type', 'like', "%{$request->keyword}%")
                    ->orWhere('question', 'like', "%{$request->keyword}%");
            })->paginate(10);

        return response()->json($essays);
    }
}
