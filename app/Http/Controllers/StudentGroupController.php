<?php



namespace App\Http\Controllers;



use App\Http\Requests\StudentGroupRequest;

use App\Services\StudentGroupService;

use App\StudentGroup;

use Illuminate\Http\Request;

use App\TeacherEnroll;
use App\TeacherEnrollLocks;
use Carbon\Carbon;

class StudentGroupController extends Controller

{

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index(Request $request)

    {

        $studentGroups = (new StudentGroupService($request))->getGroups();

        return response()->json($studentGroups);

    }

    public function getAllGroups()

    {

        $studentGroups = StudentGroup::get();

        return response()->json($studentGroups);

    }


// //////////new new new new new
    public function getTeacherGroups(Request $request){
        $date = Carbon::now()->format('Y-m');
        $locked_groups = TeacherEnrollLocks::where('teacher_id', $request->teacher_id)
                            ->where('date', $date)
                            ->pluck('student_group_id');
        $groups = StudentGroup::whereNotIn('id', $locked_groups)->get();
        if(isset($request->teacher_id) && $request->teacher_id != ''){
           $enroll_locks = TeacherEnrollLocks::where('teacher_id', $request->teacher_id)->pluck('enroll_id');
           foreach($groups as $group){
                $teacher_enroll = TeacherEnroll::whereNotIn('id', $enroll_locks)->where('teacher_id', $request->teacher_id)->where('student_group_id', $group->id)->get();
                $group->count = count($teacher_enroll);
                $group->mode = TeacherEnroll::whereNotIn('id', $enroll_locks)->where('teacher_id', $request->teacher_id)->where('student_group_id', $group->id)->first();
            } 
        }
        
        // $groups = StudentGroup::get();
        // if(isset($request->teacher_id) && $request->teacher_id != ''){
        //    foreach($groups as $group){
        //         $teacher_enroll = TeacherEnroll::where('teacher_id', $request->teacher_id)->where('student_group_id', $group->id)->get();
        //         $group->count = count($teacher_enroll);
        //     } 
        // }
        

        return $groups;
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

    public function store(StudentGroupRequest $request)

    {

        $studentGroup = new StudentGroup();

        $studentGroup->title=$request->title;

        $studentGroup->code=$request->code;

        $studentGroup->save();

        return response()->json(['msg' => 'Student Group created successfully.'], 200);

    }



    /**

     * Display the specified resource.

     *

     * @param  \App\StudentGroup  $studentGroup

     * @return \Illuminate\Http\Response

     */

    public function show($id)

    {

        return response()->json(StudentGroup::findOrFail($id), 200);

    }



    /**

     * Show the form for editing the specified resource.

     *

     * @param  \App\StudentGroup  $studentGroup

     * @return \Illuminate\Http\Response

     */

    public function edit(StudentGroup $studentGroup)

    {

        //

    }



    /**

     * Update the specified resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @param  \App\StudentGroup  $studentGroup

     * @return \Illuminate\Http\Response

     */

    public function update(StudentGroupRequest $request, $id)

    {

        $studentGroup = StudentGroup::findOrFail($id);

        $studentGroup->title=$request->title;

        $studentGroup->code=$request->code;

        $studentGroup->save();

        return response()->json(['msg' => 'Student Group Updated successfully.'], 200);

    }



    /**

     * Remove the specified resource from storage.

     *

     * @param  \App\StudentGroup  $studentGroup

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)

    {

        $studentGroup = StudentGroup::findOrFail($id);

        $studentGroup->delete();

        return response()->json(['msg' => 'Student Group has been deleted successfully'], 200);



    }

}

