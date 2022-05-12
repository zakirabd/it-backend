<?php

namespace App\Http\Controllers;

use App\Helpers\UploadHelper;
use App\Http\Requests\SpeakingRequest;
use App\Http\Requests\SpeakingAnswerRequest;
use App\Http\Requests\SpeakingAnswerReviewRequest;
use App\Services\SpeakingService;
use App\Speaking;
use App\SpeakingAnswer;
use App\SpeakingReviews;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Collection;

class SpeakingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if ($request->query('query_type') && $request->query('query_type') == 'student_answer') {
            $speaking = (new SpeakingService($request))->getSpeakingStudentAnswer();
        } else if ($request->query('query_type') && $request->query('query_type') == 'speaking_ungraded') {
            $speaking = (new SpeakingService($request))->speaking_ungraded();

        } else if ($request->query('query_type') && $request->query('query_type') == 'student_answer_tab') {
            $speaking = (new SpeakingService($request))->getSpeakingStudentAnswerById();
        } else if ($request->query('query_type') && $request->query('query_type') == 'non_reviewed') {

            $speaking = (new SpeakingService($request))->getSpeakingNonReviewed();
        } else if ($request->query('speaking_type') && $request->query('speaking_type') == 'not_review') {

            $speaking = (new SpeakingService($request))->not_review_speaking();
        } else if ($request->query('query_type') && $request->query('query_type') == 'reviewed') {

        } else {
            $speaking = (new SpeakingService($request))->getSpeaking();
        }
        return response()->json($speaking);
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(SpeakingRequest $request)
    {
        $speaking = new Speaking();
        $speaking->fill($request->all());
        $speaking->save();
        return response()->json(['msg' => 'Speaking created successfully.'], 200);
    }

    public function speakingsAnswerUpdate(Request $request, $id)
    {

        $speakingAnswer = SpeakingAnswer::findOrFail($id);
        $speakingAnswer->is_closed = !$speakingAnswer->is_closed;
        $speakingAnswer->save();

        return response()->json(['status' => 200, 'msg' => $speakingAnswer->is_closed ? 'Speaking answer closed successfully.' : 'Speaking answer recovered successfully.']);

    }

    /**
     * @param $course_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSpeakingByCourse($course_id)
    {


         $data = Speaking::with('course')->where('course_id', $course_id)->orderBy('title', 'ASC')->get();

         return response()->json($data, 200);

//        $speaking = collect($data)->sortBy(function ($i) {
//            return hexdec(last(explode(' ', trim(preg_replace('/\s+/', ' ', $i['title'])))));
//        })->values();
//
//        return response()->json($speaking, 200);
    }

    public function speakingAnswer(SpeakingAnswerRequest $request)
    {
        $checkisExist = SpeakingAnswer::where('speaking_id', $request->speaking_id)->where('user_id', $request->user_id)->first();
        if ($checkisExist) {
            $speakingAnswer = SpeakingAnswer::findOrFail($checkisExist->id);
            $speakingAnswer->fill($request->all());
            if ($request->hasFile('answer')) {
                $speakingAnswer->answer = UploadHelper::fileUpload($request->file('answer'), 'avatars');
            }
            $speakingAnswer->save();
        } else {
            $speakingAnswer = new SpeakingAnswer();
            $speakingAnswer->fill($request->all());
            if ($request->hasFile('answer')) {
                $speakingAnswer->answer = UploadHelper::fileUpload($request->file('answer'), 'avatars');
            }
            $speakingAnswer->save();
        }
        return response()->json(['msg' => 'The speaking was submitted. Your teacher will review it soon. You will be notified after your speaking is reviewed.'], 200);
    }

    public function speakingsAnswerReview(SpeakingAnswerReviewRequest $request)
    {


        $data = $request->only('review', 'speaking_answer_id', 'grade');
        $data['user_id'] = auth()->id();
        $data['is_student'] = auth()->user()->role == 'student';
        $review = SpeakingReviews::create($data);

        $total_grade = SpeakingReviews::where('speaking_answer_id', $request->speaking_answer_id)->where('is_student', 0)->sum('grade');


        $count = SpeakingReviews::where('speaking_answer_id', $request->speaking_answer_id)->where('is_student', 0)->count();

        $average_grade = $total_grade / $count;

        SpeakingAnswer::where('id', $data['speaking_answer_id'])->update(['grade' => $average_grade]);
        return response()->json(['msg' => 'Speaking Review Updated successfully.'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Speaking $speaking
     * @return \Illuminate\Http\Response
     */
    public function getAnswerbySpeking($speaking_id, $user_id)
    {
//        $speaking = Speaking::with('answare')->findOrFail($speaking_id);
//        $speaking->WhereHas('answare', function ($answare) use ($user_id) {
//            $answare->where('user_id', $user_id);
//        });

        $speaking = Speaking::with(['answare' => function ($q) use ($user_id) {
            $q->where('user_id', $user_id);
        }])->findOrFail($speaking_id);


        if (!empty($speaking->answare)) {
            if (!empty($speaking->answare[0])) {
                $get_review = SpeakingReviews::with('user')->where('speaking_answer_id', $speaking->answare[0]->id)->get();
                if (auth()->user()->role == 'student') {
                    $get_review_up = SpeakingReviews::where('speaking_answer_id', $speaking->answare[0]->id);
                    $get_review_up->update(['seen_at' => now()]);
                }
                $speaking->reviews = $get_review;
            } else {
                $speaking->reviews = array();
            }

        } else {
            $speaking->reviews = array();
        }


        return response()->json($speaking, 200);
    }

    public function show($id)
    {
        return response()->json(Speaking::findOrFail($id), 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Speaking $speaking
     * @return \Illuminate\Http\Response
     */
    public function edit(Speaking $speaking)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Speaking $speaking
     * @return \Illuminate\Http\Response
     */
    public function update(SpeakingRequest $request, $id)
    {
        $speaking = Speaking::findOrFail($id);
        $speaking->fill($request->all());
        $speaking->save();
        return response()->json(['msg' => 'Speaking Updated successfully.'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Speaking $speaking
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $speaking = Speaking::findOrFail($id);
        $speaking->delete();
        return response()->json(['msg' => 'Speaking has been deleted successfully'], 200);
    }
}
