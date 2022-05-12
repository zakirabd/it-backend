<?php

namespace App\Http\Controllers;

use App\Course;
use App\CourseAssign;
use App\Helpers\UploadHelper;
use App\Http\Requests\CourseRequest;
use App\Services\CourseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if($request->query('company_id')){
            $courses = (new CourseService($request))->getCoursesNotCompany($request->query('company_id'));
            return response()->json($courses);
        }else{
            $courses = (new CourseService($request))->getCourses();
            return response()->json($courses);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CourseRequest $request)
    {
        $course = new Course();
        $course->fill($request->all());

        if ($request->hasFile('image_url')) {
            $course->image_url = UploadHelper::imageUpload($request->file('image_url'), 'avatars');
        }
        $course->save();

        return response()->json(['msg' => 'Course created successfully.', 'data' => $course], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Course $course
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {

        return response()->json(Course::with('listenings', 'speakings')->findOrFail($id), 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Course $course
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CourseRequest $request, $id)
    {
        $course = Course::findOrFail($id);
        $course->fill($request->all());

        if ($request->hasFile('image_url')) {
            $course->image_url = UploadHelper::imageUpload($request->file('image_url'), 'avatars');
        }
        $course->save();

        return response()->json(['msg' => 'Course Updated successfully.'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Course $course
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        CourseAssign::where('course_id', $id)->delete();

        if ($course->image_url) {
            Storage::delete('public/' . $course->avatar_url);
        }

        $course->delete();

        return response()->json(['msg' => 'Course has been deleted successfully'], 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function lessons($id)
    {
        $lessons = Course::findOrFail($id)->lessons;

        return response()->json($lessons);
    }
}
