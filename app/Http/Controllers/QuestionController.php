<?php

namespace App\Http\Controllers;

use App\Answer;
use App\Helpers\UploadHelper;
use App\Http\Requests\QuestionRequest;
use App\Question;
use App\Services\QuestionService;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->query('user_type') && $request->query('user_type') == 'student') {
            $questions = (new QuestionService($request))->getStudentQuestion();
            return response()->json(['status' => 200, 'data' => $questions, 'timeZone' => config('app.timezone')]);
        } else {
            $questions = (new QuestionService($request))->getQuestion();
            return response()->json($questions);

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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(QuestionRequest $request)
    {

        $question = new Question();
        $question->fill($request->all());
        if ($request->hasFile('question_image')) {
            $question->question_image = UploadHelper::imageUploadOriginalSize($request->file('question_image'), 'avatars');
        }
        if ($request->hasFile('audio_file')) {
            $question->audio_file = UploadHelper::fileUpload($request->file('audio_file'), 'avatars');
        }
        if ($request->hasFile('video_file')) {
            $question->video_file = UploadHelper::fileUpload($request->file('video_file'), 'videos');
        }
        $question->save();
        $question_option = json_decode($request->question_option, true);
        if (!empty($question_option)) {
            foreach ($question_option as $key => $item) {
                if ($item['type'] != 'parent') {
                    $answer = new Answer();
                    $answer->question_id = $question->id;
                    $title = $item['title'];
                    if ($item['type'] == 'matching_type') {
                        if ($request->sub_type == 'text_to_image' || $request->sub_type == 'image_to_image') {
                            if (isset($request->file('text_to_image')[$key])) {
                                $check = UploadHelper::imageUpload($request->file('text_to_image')[$key], 'avatars');
                            } else {
                                $check = '';
                            }
                        }
                        if ($request->sub_type == 'image_to_image') {
                            if (isset($request->file('image_to_image')[$key])) {
                                $title = UploadHelper::imageUpload($request->file('image_to_image')[$key], 'avatars');
                            } else {
                                $title = '';
                            }
                        }
                        if ($request->sub_type == 'text_to_text') {
                            $check = $item['check'];
                        }

                    } else if ($item['type'] == 'single_image_choice') {
                        if (isset($request->file('single_image_choice')[$key])) {
                            $title = UploadHelper::imageUpload($request->file('single_image_choice')[$key], 'avatars');
                        } else {
                            $title = '';
                        }
                        $check = $item['check'];
                    } else if ($item['type'] == 'single_audio_with_image') {
                        if (isset($request->file('single_audio_with_image')[$key])) {
                            $title = UploadHelper::imageUpload($request->file('single_audio_with_image')[$key], 'avatars');
                        } else {
                            $title = '';
                        }
                        $check = $item['check'];
                    } else {
                        $check = 0;
                        if ($item['check']) {
                            $check = 1;
                        }
                    }
                    if ($item['type'] == 'dropdown_question_type') {
                        $get_ans_string = $this->get_string_between($title, '[', ']');
                        $answer_t = explode(',', $get_ans_string);
                        if (!empty($answer_t)) {
                            $check = $answer_t[0];
                        }
                    }
                    $answer->title = $title;
                    $answer->is_correct = $check;
                    $answer->score = $item['points'];
                    $answer->save();
                }

            }
        }
        return response()->json(['msg' => 'Question created successfully.'], 200);
    }

    public function questionReorder(Request $request)
    {
        $order = json_decode($request->order);
        foreach ($order as $index_id => $question) {

            if (!empty($question->id)) {
                $parent_question = Question::find($question->id);
                $parent_question->parent_id = null;
                $parent_question->sort_id = $index_id;
                $parent_question->save();
            }
            if (isset($question->children[0]->id)) {
                foreach ($question->children as $sort_id => $child_question) {
                    if (isset($child_question->id)) {
                        $current_question = Question::find($child_question->id);
                        $current_question->sort_id = $sort_id;
                        $current_question->parent_id = $question->id;
                        $current_question->save();
                    }
                }

            } else {

                if (isset($question->id)) {
                    $parent_question = Question::find($question->id);
                    $parent_question->parent_id = null;
                    $parent_question->sort_id = $index_id;
                    $parent_question->save();
                }
            }
        }
//        return $order;
        return response()->json(['msg' => 'Question order update  successfully.'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Question $question
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $question = Question::with('answers')->where('id', $id)->first();

        if ($question->type == 'matching_type') {
            foreach ($question->answers as $key => $answer) {
                if ($question->sub_type == 'text_to_image' || $question->sub_type == 'image_to_image') {
                    $question->answers[$key]->is_correct = asset("/storage/{$answer->is_correct}");
                }
                if ($question->sub_type == 'image_to_image') {
                    $question->answers[$key]->title = asset("/storage/{$answer->title}");
                }
            }
        }
        if ($question->type == 'single_image_choice') {
            foreach ($question->answers as $key => $answer) {
                $question->answers[$key]->title = asset("/storage/{$answer->title}");
            }
        }


        return response()->json($question, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Question $question
     * @return \Illuminate\Http\Response
     */
    public function edit(Question $question)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Question $question
     * @return \Illuminate\Http\Response
     */
    public function get_string_between($str, $from, $to)
    {
        $string = substr($str, strpos($str, $from) + strlen($from));
        if (strstr($string, $to, TRUE) != FALSE) {
            $string = strstr($string, $to, TRUE);
        }
        return $string;

    }

    public function update(QuestionRequest $request, $id)
    {


        $question = Question::findOrFail($id);
        $question->fill($request->all());
        if ($request->hasFile('question_image')) {
            $question->question_image = UploadHelper::imageUploadOriginalSize($request->file('question_image'), 'avatars');
        }
        if ($request->hasFile('audio_file')) {
            $question->audio_file = UploadHelper::fileUpload($request->file('audio_file'), 'avatars');
        }
        if ($request->hasFile('video_file')) {
            $question->video_file = UploadHelper::fileUpload($request->file('video_file'), 'videos');
        }
        $question->save();
        $question_option = json_decode($request->question_option, true);
        $array_title = array();
        $array_ids = array();
        if (!empty($question_option)) {
            foreach ($question_option as $key => $item) {
                if ($item['type'] != 'parent') {
                    $array_title[] = $item['title'];
                    $check = 0;
                    if (isset($item['id'])) {
                        $array_ids[] = $item['id'];
                        $answer_check = Answer::where('question_id', $question->id)->where('id', $item['id'])->first();
                        if ($answer_check) {
                            $answer = Answer::findOrFail($item['id']);
                            $answer->question_id = $question->id;
                            if ($item['type'] == 'matching_type') {
                                if ($request->sub_type == 'text_to_image' || $request->sub_type == 'image_to_image') {
                                    if (isset($request->file('text_to_image')[$key])) {
                                        $check = UploadHelper::imageUpload($request->file('text_to_image')[$key], 'avatars');
                                    }
                                }
                                if ($request->sub_type == 'image_to_image') {
                                    if (isset($request->file('image_to_image')[$key])) {
                                        $title = UploadHelper::imageUpload($request->file('image_to_image')[$key], 'avatars');
                                        $answer->title = $title;
                                        $array_title[] = $title;
                                    }
                                } else {
                                    $title = $item['title'];
                                    $answer->title = $title;
                                }
                                if ($request->sub_type == 'text_to_text') {
                                    $check = $item['check'];
                                }
                                if (!empty($check)) {
                                    $answer->is_correct = $check;
                                }
                            } else if ($item['type'] == 'single_image_choice') {
                                if (isset($request->file('single_image_choice')[$key])) {
                                    $title = UploadHelper::imageUpload($request->file('single_image_choice')[$key], 'avatars');
                                    $answer->title = $title;
                                    $array_title[] = $title;
                                }
                                $check = 0;
                                if ($item['check']) {
                                    $check = 1;
                                }
                                $answer->is_correct = $check;
                            } else if ($item['type'] == 'single_audio_with_image') {
                                if (isset($request->file('single_audio_with_image')[$key])) {
                                    $title = UploadHelper::imageUpload($request->file('single_audio_with_image')[$key], 'avatars');
                                    $answer->title = $title;
                                    $array_title[] = $title;
                                }
                                $check = 0;
                                if ($item['check']) {
                                    $check = 1;
                                }
                                $answer->is_correct = $check;
                            } else {
                                $title = $item['title'];
                                $check = 0;
                                if ($item['check']) {
                                    $check = 1;
                                }
                                if ($item['type'] == 'dropdown_question_type') {
                                    $get_ans_string = $this->get_string_between($title, '[', ']');
                                    $answer_t = explode(',', $get_ans_string);
                                    if (!empty($answer_t)) {
                                        $check = $answer_t[0];
                                    }
                                }
                                $answer->title = $title;
                                $answer->is_correct = $check;
                            }
                            $answer->score = $item['points'];

                            $answer->save();
                        }
                    } else {
                        $answer = new Answer();
                        $answer->question_id = $question->id;
                        $title = $item['title'];
                        if ($item['type'] == 'matching_type') {
                            if ($request->sub_type == 'text_to_image' || $request->sub_type == 'image_to_image') {
                                if (isset($request->file('text_to_image')[$key])) {
                                    $check = UploadHelper::imageUpload($request->file('text_to_image')[$key], 'avatars');
                                } else {
                                    $check = '';
                                }
                            }
                            if ($request->sub_type == 'image_to_image') {
                                if (isset($request->file('image_to_image')[$key])) {
                                    $title = UploadHelper::imageUpload($request->file('image_to_image')[$key], 'avatars');
                                    $array_title[] = $title;

                                } else {
                                    $title = '';
                                }
                            } else {
                                $title = $item['title'];
                            }
                            if ($request->sub_type == 'text_to_text') {
                                $check = $item['check'];
                            }

                        } else if ($item['type'] == 'single_image_choice') {
                            if (isset($request->file('single_image_choice')[$key])) {
                                $title = UploadHelper::imageUpload($request->file('single_image_choice')[$key], 'avatars');
                                $answer->title = $title;
                                $array_title[] = $title;
                            }
                            $check = 0;
                            if ($item['check']) {
                                $check = 1;
                            }
                            $answer->is_correct = $check;
                        } else if ($item['type'] == 'single_audio_with_image') {
                            if (isset($request->file('single_audio_with_image')[$key])) {
                                $title = UploadHelper::imageUpload($request->file('single_audio_with_image')[$key], 'avatars');
                                $answer->title = $title;
                                $array_title[] = $title;
                            }
                            $check = 0;
                            if ($item['check']) {
                                $check = 1;
                            }
                            $answer->is_correct = $check;
                        } else {
                            $check = 0;
                            if ($item['check']) {
                                $check = 1;
                            }
                        }
                        if ($item['type'] == 'dropdown_question_type') {
                            $get_ans_string = $this->get_string_between($title, '[', ']');
                            $answer_t = explode(',', $get_ans_string);
                            if (!empty($answer_t)) {
                                $check = $answer_t[0];
                            }
                        }
                        $answer->title = $title;
                        $answer->is_correct = $check;
                        $answer->score = $item['points'];
                        $answer->save();
                    }
                }
            }
        }
        if ($request->sub_type == 'image_to_image' || $request->type == 'single_image_choice' || $request->type == 'single_audio_with_image') {
            $answer_relation_check = Answer::where('question_id', $id)->get();
            foreach ($answer_relation_check as $relation_answer) {
                if (!in_array($relation_answer->title, $array_title)) {
                    if (!in_array($relation_answer->id, $array_ids)) {
                        $answer_data = Answer::findOrFail($relation_answer->id);
                        $answer_data->delete();
                    }
                }
            }
        } else {
            $answer_relation_check = Answer::where('question_id', $id)->get();
            foreach ($answer_relation_check as $relation_answer) {
                if (!in_array($relation_answer->title, $array_title)) {
                    $answer_data = Answer::findOrFail($relation_answer->id);
                    $answer_data->delete();
                }
            }
        }
        return response()->json(['msg' => 'Question Updated successfully.'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Question $question
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $checkIsParent = Question::where('parent_id', $id)->get();
        if (count($checkIsParent) == 0) {
            $question = Question::findOrFail($id);
            $question->delete();
            return response()->json(['msg' => 'Question has been deleted successfully'], 200);
        } else {
            return response()->json(['msg' => 'Question is not deleted successfully'], 200);

        }
    }
}
