<?php

namespace App\Http\Controllers;

use App\Essay;
use App\EssayAnswer;
use App\EssayReview;
use App\Http\Requests\EssayReviewRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EssayReviewController extends Controller
{
    /**
     * Essay review store in db.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(EssayReviewRequest $request)
    {
        $user = auth()->user();
        $essayAnswer = EssayAnswer::with('essay')->findorFail($request->essay_answer_id);
        $essayReviewSet               = $request->only('review', 'essay_answer_id', 'grade', 'rating');
        $essayReviewSet['user_id']    = $user->id;
        $essayReviewSet['is_student'] = $user->role == 'student';
        $review = EssayReview::create($essayReviewSet);
        $average_grade = EssayReview::where('essay_answer_id', $request->essay_answer_id)->where('is_student', 0)->avg('grade');
        EssayAnswer::where('id', $essayReviewSet['essay_answer_id'])->update(['grade' => number_format($average_grade)]);

        // mid term essay for head teacher
        if ($essayAnswer->essay->essay_type == 'midterm_end_course') {
            $review = EssayReview::where('essay_answer_id', $request->essay_answer_id)
                ->update([
                    'head_teacher_review'  => $request->review,
                    'head_teacher_confirm' => $request->head_teacher_confirm == true ? 1 : 0,
                ]);
        }

        return response()->json(['msg'=> $user->role == 'student'
            ? 'Thanks for the replay. Your teacher will review and let you know.'
            : 'Thanks for the feedback. Please ask your student to see your reviews.', 'data' => $review]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {

        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'required|integer|exists:essay_reviews,id',
        ]);

        EssayReview::whereHas('user', function ($q) {
            if (auth()->user()->role != 'student') {
                $q->where('role', 'student');
            }
        })
            ->whereIn('id', $request->ids)
            ->where('user_id', '!=', auth()->id())
            ->update(['seen_at' => now()]);

        return response()->json(EssayReview::with('user')->find($request->ids));
    }

    // public function getAll()
    // {   
    //     $users = DB::table('essay_reviews')->where('user_id', '562')->get();
    //      return response()->json($users);
    // }

}
