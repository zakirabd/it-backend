<?php



namespace App\Http\Controllers;



use App\Http\Requests\TeacherEnrollRequest;

use App\Services\TeacherEnrollsService;

use App\TeacherEnroll;

use Illuminate\Http\Request;

use App\StudentsLog;

// ////new 
use App\TeacherEnrollsLog;
use App\Attendance;

class TeacherEnrollController extends Controller

{

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index(Request $request)

    {
        if ($request->query('request_type') && $request->query('request_type') == 'teacher_enroll'){

            $teacherEnrollsService = (new TeacherEnrollsService($request))->getTeacherStudents();


            return response()->json($teacherEnrollsService);
            
        }else{
            
            $teacherEnrollsService = (new TeacherEnrollsService($request))->getEnrolls();


            return response()->json($teacherEnrollsService);
        }

        // $teacherEnrollsService = (new TeacherEnrollsService($request))->getEnrolls();

        // return response()->json($teacherEnrollsService);

    }

    
    // ///////////////////////////////////////new new new new new ////////////////////

    public function changeStudentEnroll(Request $request, $id){
        $teacher_enroll = TeacherEnroll::findOrFail($id);
        $teacher_enroll->fill($request->all());
        $check_in = json_decode($request->check_in, true);
        $teacher_enroll->save();
        foreach($check_in as $item){
            $attendance = Attendance::findOrFail($item['id']);
            $attendance->lesson_mode = $request->lesson_mode;
            $attendance->teacher_id = $request->teacher_id;
            $attendance->lesson_houre = $request->lesson_houre;
            $attendance->student_group_id = $request->student_group_id;
            $attendance->study_mode = $request->study_mode;
            $attendance->save();
        }
        // 

        // 
        return response()->json(['msg'=> 'Student Enroll updated successfully']);
    }

    public function teacherGroupUpdate(Request $request){
        $id = json_decode($request->id, true);
       
        foreach($id as $item){
            $teacher_enroll = TeacherEnroll::findOrFail($item['id']);
            $teacher_enroll->lesson_houre = $request->lesson_houre;
            
            $teacher_enroll->save();
        }
        return response()->json(['msg' => 'Group Updated Successfully.']);
        return $arr;
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

    public function store(TeacherEnrollRequest $request)

    {

        if($request->student_group_id == '999' || $request->student_group_id == '9999'){

            $teacherEnroll = new TeacherEnroll();

            $teacherEnroll->fill($request->all());

            if($request->lesson_mode == 'Trial class'){
                $teacherEnroll->trial_mode = 1;
            }

            $teacherEnroll->save();

            // new line for students log

            $students_log = new StudentsLog();

            $students_log->student_id = $request->student_id;

            $students_log->enroll_type = 'teacher';

            $students_log->type = 'add';

            $students_log->teacher_id = $request->teacher_id;

            $students_log->lesson_mode = $request->lesson_mode;

            $students_log->study_mode = $request->study_mode;

            $students_log->save();

            return response()->json(['msg' => 'Teacher Enroll created successfully.'], 200);

        }else{
            $check = TeacherEnroll::where('student_id', $request->student_id)->where('student_group_id', $request->student_group_id)->get();

            if(count($check) == 0){
                $teacherEnroll = new TeacherEnroll();


                $teacherEnroll->fill($request->all());
                // ///////// new line for trial mode

                if($request->lesson_mode == 'Trial class'){
                    $teacherEnroll->trial_mode = 1;
                }


                $teacherEnroll->save();
                
                // new line for students log

                $students_log = new StudentsLog();

                $students_log->student_id = $request->student_id;

                $students_log->enroll_type = 'teacher';

                $students_log->type = 'add';

                $students_log->teacher_id = $request->teacher_id;
            

                $students_log->lesson_mode = $request->lesson_mode;


                $students_log->study_mode = $request->study_mode;

                $students_log->save();

                // /////////////

            
                return response()->json(['msg' => 'Teacher Enroll created successfully.'], 200);
            }else{
                return response()->json(['msg' => 'Student Already Exists.', 'type' => 'exists'], 200);
            }
        }

        

    }



    /**

     * Display the specified resource.

     *

     * @param  \App\TeacherEnroll  $teacherEnroll

     * @return \Illuminate\Http\Response

     */

    public function show($id)

    {

        return response()->json(TeacherEnroll::findOrFail($id), 200);

    }



    /**

     * Show the form for editing the specified resource.

     *

     * @param  \App\TeacherEnroll  $teacherEnroll

     * @return \Illuminate\Http\Response

     */

    public function edit(TeacherEnroll $teacherEnroll)

    {

        //

    }



    /**

     * Update the specified resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @param  \App\TeacherEnroll  $teacherEnroll

     * @return \Illuminate\Http\Response

     */

    public function update(TeacherEnrollRequest $request, $id)

    {



        $teacherEnroll = TeacherEnroll::findOrFail($id);

        // /////////////////////new line  
       if($teacherEnroll->teacher_id != $request->teacher_id){
            $teacher_enroll_logs = new TeacherEnrollsLog();

            $teacher_enroll_logs->student_id = $teacherEnroll->student_id;
            $teacher_enroll_logs->teacher_id = $teacherEnroll->teacher_id;
            $teacher_enroll_logs->lesson_mode = $teacherEnroll->lesson_mode;
            $teacher_enroll_logs->lesson_houre = $teacherEnroll->lesson_houre;
            $teacher_enroll_logs->study_mode = $teacherEnroll->study_mode;
            $teacher_enroll_logs->student_group_id = $teacherEnroll->student_group_id;
            $teacher_enroll_logs->fee = $teacherEnroll->fee;
           
            $teacher_enroll_logs->save();
       }

        $teacherEnroll->fill($request->all());

        if($request->lesson_mode == 'Trial class'){
            $teacherEnroll->trial_mode = 1;
        }


        $teacherEnroll->save();

        return response()->json(['msg' => 'Teacher Enroll Updated successfully.'], 200);

    }



    /**

     * Remove the specified resource from storage.

     *

     * @param  \App\TeacherEnroll  $teacherEnroll

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)

    {

        $teacherEnroll = TeacherEnroll::findOrFail($id);

        $teacher_enroll_logs = new TeacherEnrollsLog();

        $teacher_enroll_logs->student_id = $teacherEnroll->student_id;
        $teacher_enroll_logs->teacher_id = $teacherEnroll->teacher_id;
        $teacher_enroll_logs->lesson_mode = $teacherEnroll->lesson_mode;
        $teacher_enroll_logs->lesson_houre = $teacherEnroll->lesson_houre;
        $teacher_enroll_logs->study_mode = $teacherEnroll->study_mode;
        $teacher_enroll_logs->student_group_id = $teacherEnroll->student_group_id;
        $teacher_enroll_logs->fee = $teacherEnroll->fee;

        $teacher_enroll_logs->save();
        // ///////////////

        
        $students_log = new StudentsLog();

        
        $students_log->student_id = $teacherEnroll->student_id;

        $students_log->enroll_type = 'teacher';

        $students_log->type = 'remove';

        $students_log->teacher_id = $teacherEnroll->teacher_id;

        $students_log->lesson_mode = $teacherEnroll->lesson_mode;

        $students_log->study_mode = $teacherEnroll->study_mode;

        $students_log->save();


        

        

        $teacherEnroll->delete();

        return response()->json(['msg' => 'Teacher Enroll has been deleted successfully'], 200);



    }

}

