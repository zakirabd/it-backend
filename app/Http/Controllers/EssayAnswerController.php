<?php



namespace App\Http\Controllers;



use App\Essay;

use App\EssayAnswer;

use App\Services\EssayAnswerService;

use App\User;

use Carbon\Carbon;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;

// new 
use Illuminate\Support\Str;
use App\Plagiarism;

class EssayAnswerController extends Controller

{

    /**

     * Display a listing of the resource.

     *

     * @param \Illuminate\Http\Request $request

     * @return \Illuminate\Http\JsonResponse

     */

    public function index(Request $request)

    {



        if ($request->query('type') && $request->query('type') == 'non_reviewed') {

            $essayAnswers = (new EssayAnswerService($request))->nonReviewedAnswers();

        } elseif ($request->query('type') && $request->query('type') == 'closed') {

            $essayAnswers = (new EssayAnswerService($request))->closedAnswers();

        } elseif ($request->query('type') && $request->query('type') == 'essay_not_review') {

            $essayAnswers = (new EssayAnswerService($request))->EssayNotReview();

        } elseif ($request->query('type') && $request->query('type') == 'NonReviewCeltEssay') {

            $essayAnswers = (new EssayAnswerService($request))->NonReviewCeltEassay();

        } elseif ($request->query('type') && $request->query('type') == 'celt_essays') {

            $essayAnswers = (new EssayAnswerService($request))->celtEssays();

        } elseif ($request->query('page')) {



            $essayAnswers = (new EssayAnswerService($request))->getEssaysList();

        } else {

            $essayAnswers = (new EssayAnswerService($request))->reviews();

        }

        return response()->json($essayAnswers);

    }

    
// check plagiarism
    public function checkPlagiarism(Request $request){
        $check_exist = Plagiarism::where('checked_essay_id', $request->essay_id)->get();

        if(count($check_exist) != 0){
            $check_not_pg = Plagiarism::where('checked_essay_id', $request->essay_id)
            ->where('matched_essay_id', null)
            ->where('percentage', '0')->get();

            if(count($check_not_pg) != 0){
                $pg_not = [];
                $pg_not['type'] = 'not_plagiat';
               

                return [$pg_not];
            }else{
                $check_pg = Plagiarism::where('checked_essay_id', $request->essay_id)
                ->where('matched_essay_id', '!=', null)->get();

                $plagiat_essays = [];

                if(count($check_pg) != 0){
                    foreach($check_pg as $item){
                       $result = [];
                       
                        $essay = EssayAnswer::where('id', $item->matched_essay_id)->with('user')->with('essay')->first();
                        $result['essay'] =$essay;
                        $result['percentage'] = $item->percentage;
                        array_push($plagiat_essays, $result);
                    }
                }
                $final_result = [];
                $final_result['type'] = 'plagiat';
                $final_result['plagiat'] = $plagiat_essays;
                return [$final_result];

            }
        }else{
                $essay = EssayAnswer::findOrFail($request->essay_id);
                $plagiarism = [];
                if($request->type == 'movie'){

                    $movie_essay = EssayAnswer::where('id', '!=', $request->essay_id)->with('user')->with('essay')
                    ->whereHas('essay', function($q){
                        $q->where('title', 'like', "%Movie%");
                    })->whereHas('user', function ($q) use  ($request){
                        $q->where('company_id', $request->company_id);
                    })->get();

                    foreach($movie_essay as $item){
                        $text1 = $item->answer;
                        $text2 = $essay->answer;
                        $sim = similar_text(preg_replace('/\s\s+/', ' ',trim(strip_tags($text1))), preg_replace('/\s\s+/', ' ',trim(strip_tags($text2))), $perc);
                        $test = [];
                        if($perc > 50){
                            $test['essay'] = $item;
                            $test['percent'] = $perc;
                            array_push($plagiarism,  $test);
                        }
                    
                    }
                }else{
                    $other_essays = EssayAnswer::where('id', '!=', $request->essay_id)
                    ->where('essay_id', $request->other_essay_id)
                    ->with('user')->with('essay')
                    ->whereHas('user', function ($q) use  ($request){
                            $q->where('company_id', $request->company_id);
                        })
                    ->get();

                    foreach($other_essays as $item){
                        $text1 = $item->answer;
                        $text2 = $essay->answer;
                        $sim = similar_text(preg_replace('/\s\s+/', ' ',trim(strip_tags($text1))), preg_replace('/\s\s+/', ' ',trim(strip_tags($text2))), $perc);
                        $test = [];
                        if($perc > 50){
                            $test['essay'] = $item;
                            $test['percent'] = $perc;
                            array_push($plagiarism,  $test);
                        }
                    
                    }
                }

                if(count($plagiarism) != 0){
                    foreach($plagiarism as $item){
                        $pg = new Plagiarism();
                        $pg->checked_essay_id = $request->essay_id;
                        $pg->matched_essay_id = $item['essay']->id;
                        $pg->percentage = $item['percent'];
                        $pg->save();   
                    }

                    $check_pg = Plagiarism::where('checked_essay_id', $request->essay_id)
                    ->where('matched_essay_id', '!=', null)->get();

                    $plagiat_essays = [];

                    if(count($check_pg) != 0){
                        foreach($check_pg as $item){
                           $result = [];
                            $essay = EssayAnswer::where('id', $item->matched_essay_id)->with('user')->with('essay')->first();
                            $result['essay'] =$essay;
                            $result['percentage'] = $item->percentage;
                            array_push($plagiat_essays, $result);
                        }
                    }
                    $final_result = [];
                    $final_result['type'] = 'plagiat';
                    $final_result['plagiat'] = $plagiat_essays;
                    return [$final_result];
                  
                }else{
                    $pg = new Plagiarism();
                    $pg->checked_essay_id = $request->essay_id;
                    $pg->matched_essay_id = null;
                    $pg->percentage = '0';
                    $pg->save();

                    $pg_not = [];
                   
                    $pg_not['type'] = 'not_plagiat';

                    return [$pg_not];
                }
                
                // if(count($check_not_pg) != 0){
                //     $pg_not = [];
                //     $pg_not['name'] = null;
                //     $pg_not['unit'] = null;
                //     $pg_not['percentage'] = null;

                //     return $pg_not;
                // }
            }
   
    }


    /**

     * Store a newly created resource in storage.

     *

     * @param \Illuminate\Http\Request $request

     * @return \Illuminate\Http\JsonResponse

     */

    public function store(Request $request)

    {

        // Validate form data

        //return  $request->all();



        $request->validate([

            'id' => 'nullable|integer|exists:essay_answers,id',

            'essay_id' => 'required|integer|exists:essays,id',

            'answer' => 'required|string',



        ]);





        $data['user_id'] = auth()->id();



        EssayAnswer::updateOrCreate(

            [

                'user_id' => auth()->id(),

                'essay_id' => $request->essay_id,

            ],

            [

                'answer' => $request->answer,

                'is_submitted' => $request->is_submitted,

                'essay_id' => $request->essay_id,

                'user_id' => auth()->id(),

                'submit_date' => $request->is_submitted == 1 ? Carbon::today() : null

            ]

        );



        return response()->json(['status' => 200, 'msg' => 'The Essay was submitted. Your teacher will review it soon. You will be notified after your essay is reviewed.']);

    }



    /**

     * Update the specified resource in storage.

     *

     * @param \Illuminate\Http\Request $request

     * @param int $id

     * @return \Illuminate\Http\JsonResponse

     */

    public function update(Request $request, $id)

    {

        $essayAnswer = EssayAnswer::findOrFail($id);

        $essayAnswer->is_closed = !$essayAnswer->is_closed;

        $essayAnswer->save();



        return response()->json(['status' => 200, 'msg' => $essayAnswer->is_closed ? 'Essay answer closed successfully.' : 'Essay answer recovered successfully.']);

    }



    public function essayUngraded(Request $request)

    {



        $final_data = array();

        $get_company_students = User::where('role', 'student')->where('company_id', auth()->user()->company_id)->pluck('id');

        $essay_ungraded = EssayAnswer::with('essay', 'user', 'teachers')

            ->withCount('teachers')

            ->orderBy('teachers_count', 'desc')

            ->where('grade', null)

            ->where('is_submitted', 1)

            ->where('is_closed', 0)

            ->whereIn('user_id', $get_company_students)->get();

        $index = 0;



        foreach ($essay_ungraded as $key_main => $teacher) {



            foreach ($teacher->teachers as $key => $item) {

                if(Str::contains($item->lesson_mode, 'English')){

                    $teacher->teachers[$key]['teacher_name'] = DB::table('users')->where('id', $item->teacher_id)

                        ->select([

                            'users.first_name', 'users.last_name',

                            DB::raw("CONCAT(users.first_name, '  ', users.last_name) as full_name"),

                        ])->first();

                    $final_data[$index]['teacher'] = $teacher->teachers[$key]->teacher_name->full_name;

                    $final_data[$index]['title'] = $teacher->essay->title;

                    $final_data[$index]['date'] = $teacher->submit_date;



                    $final_data[$index]['student_info'] = $teacher->user->full_name;

                    $index++;
                }

            }

        }

        $sort_data = array_column($final_data, 'teacher');

        array_multisort($final_data, SORT_DESC, $sort_data);

        return response()->json($final_data);

    }

}

