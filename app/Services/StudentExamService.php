<?php



namespace App\Services;



use App\Advanced;

use App\Http\Resources\ExamResult\MonthlyExamResult;

use App\StudentExam;

use App\TeacherEnroll;

use Carbon\Carbon;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Str;

use PDF;



class StudentExamService

{



    private $request;

    private $paginate;



    public function __construct($request = null, $paginate = 20)

    {

        $this->request = $request;

        $this->paginate = $paginate;



    }



    /**

     * @param $query_sql

     * @return mixed

     */

    public function searchFilter($query_sql)

    {

        return $query_sql->where(function ($query) {

            $query->where('exam_title', 'like', "%{$this->request->keyword}%")

                ->orWhereHas('student', function ($q) {

                    $q->where(DB::raw("concat(first_name,' ',last_name)"), 'like', "%{$this->request->keyword}%");

                })

                ->orWhereHas('exam', function ($exam) {

                    $exam->WhereHas('course', function ($course) {

                        $course->where('level', 'like', "%{$this->request->keyword}%");

                    });

                });



        });

    }



    /**

     * @param $sql

     * @return mixed

     */

    public function getExamFilter($sql)

    {

        if ($this->request->resultType == 'Passed') {

            $sql->where('score', '>=', 70);



        }

        if ($this->request->resultType == 'Failed') {

            $sql->where('score', '<', 70);

        }



        if ($this->request->orderField == 'id' || $this->request->orderField == 'exam_title') {



            $sql->orderBy($this->request->orderField, $this->request->orderMode);

        }



        if ($this->request->orderField == 'first_name') {

            $sql->join('users', 'users.id', '=', 'student_exams.student_id')

                ->orderBy('users.first_name', $this->request->orderMode);



        }



        return $sql->select('student_exams.*');



    }





    /**

     * get student homework result list

     * @param $request

     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator

     */



    public function getHomeworkResult()

    {



        $sql = StudentExam::select('id', 'exam_title', 'updated_at', 'score', 'status')

            ->where(['student_id' => $this->request->student_id, 'start_time' => 0, 'is_submit' => 1])

            ->orderBy('updated_at', 'DESC');

        if ($this->request->keyword != '') {

            $sql->where('exam_title', 'like', "%{$this->request->keyword}%");

        }

        return $sql->paginate(20);

        

    }



    /**

     * Get Student Exam result

     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator

     */

    public function getExamResult()

    {

        // $nowStr     = date("Y-m-d H:i:s");


        $sql = StudentExam::with('exam.course')->where('student_id', $this->request->student_id)

             ->where('is_submit', 1)
            // ->whereRaw('DATE_ADD(start_time, INTERVAL duration MINUTE) < "'. $nowStr . '"')

            ->where('start_time', '>', 0);



        if ($this->request->keyword != '') {

            $sql->where('exam_title', 'like', "%{$this->request->keyword}%");

        }



        return $sql->orderBy('updated_at', 'DESC')->paginate(20);

    }



    /**

     * Get month wise exam result

     * @param Request $request

     * @return \Illuminate\Http\JsonResponse

     *

     */

    public function getMonthWiseExamResult()

    {



        $date = Carbon::parse($this->request->date);



        $sql = StudentExam::with(['student', 'exam:id,title'])

            ->where('is_submit', 1)

            ->where('duration', '>', 0)

            ->orderBy('updated_at', 'DESC')

            ->whereMonth('student_exams.updated_at', $date->format('m'))

            ->whereYear('student_exams.updated_at', $date->format('Y'))

            ->whereHas('student', function ($student) {

                $student->where('company_id', auth()->user()->company->id);

            });



        if ($this->request->keyword != '') {

            $sql = $this->searchFilter($sql);

        }



        $sql = $this->getExamFilter($sql);





        return $sql->take($this->request->page * 20)->get();

    }

    public function getMonthWiseHomeworkResult(){
        $date = Carbon::parse($this->request->date);

        $sql = StudentExam::with(['student', 'exam:id,title'])
            ->where('is_submit', 1)
            ->where('duration', '=', 0)
            ->orderBy('updated_at', 'DESC')
            ->whereMonth('student_exams.updated_at', $date->format('m'))
            ->whereYear('student_exams.updated_at', $date->format('Y'))
            ->whereHas('student', function ($student) {
                $student->where('company_id', auth()->user()->company->id);
            });

        if ($this->request->keyword != '') {
            $sql = $this->searchFilter($sql);
        }

        $sql = $this->getExamFilter($sql);


        return $sql->take($this->request->page * 20)->get();
    }

    
    //  SUPER ADMIN EXAM AND HOME WORK RESULTS CODE

    public function getSuperAdminHwExamResults(){
            if($this->request->type == 'super-admin-exam-result' && $this->request->company_id != ''){
                
                $date = Carbon::parse($this->request->date);

                $sql = StudentExam::with(['student', 'exam:id,title'])
                    ->where('is_submit', 1)
                    ->where('duration', '>', 0)
                    ->orderBy('updated_at', 'DESC')
                    ->whereMonth('student_exams.updated_at', $date->format('m'))
                    ->whereYear('student_exams.updated_at', $date->format('Y'))
                    ->whereHas('student', function ($student) {
                        $student->where('company_id', $this->request->company_id);
                    });

                if ($this->request->keyword != '') {
                    $sql = $this->searchFilter($sql);
                }

                $sql = $this->getExamFilter($sql);


                return $sql->take($this->request->page * 20)->get();

            }else if($this->request->type == 'super-admin-homework-result' && $this->request->company_id != ''){
                
                $date = Carbon::parse($this->request->date);

                $sql = StudentExam::with(['student', 'exam:id,title'])
                    ->where('is_submit', 1)
                    ->where('duration', '=', 0)
                    ->orderBy('updated_at', 'DESC')
                    ->whereMonth('student_exams.updated_at', $date->format('m'))
                    ->whereYear('student_exams.updated_at', $date->format('Y'))
                    ->whereHas('student', function ($student) {
                        $student->where('company_id', $this->request->company_id);
                    });

                if ($this->request->keyword != '') {
                    $sql = $this->searchFilter($sql);
                }

                $sql = $this->getExamFilter($sql);


                return $sql->take($this->request->page * 20)->get();
            }else if($this->request->company_id == ''){
                return [];
            }
        
        
    }

    /**

     * Get last 30 days exam result  include today as well

     * @param Request $request

     * @return \Illuminate\Http\JsonResponse

     */

    public function getMonthlyExamResult()

    {



        $previous_days = Carbon::now()->subDays(29)->format('Y-m-d h:i');

        $get_assign_student = TeacherEnroll::where('teacher_id', auth()->user()->id)->where('status', '1')->pluck('student_id')->toArray();



        $sql = StudentExam::where('is_submit', 1)->where('duration', '>', 0)->where('updated_at', '>=', $previous_days)

            ->whereIn('student_id', array_unique($get_assign_student))

            ->with(['student:id,first_name,Last_name', 'exam.course:id,level'])

            ->select('id','student_id','score','spend_time','exam_title','exam_id', 'updated_at', 'created_at');



        if ($this->request->keyword != '') {

            $sql = $this->searchFilter($sql);

        }



        // FIXME : Data resource or optimize.



        return $sql->orderBy('updated_at', 'DESC')->take($this->request->page*20)->get();



    }



    /**

     * Get last 30 days home work  result  include today as well

     * @param Request $request

     * @return \Illuminate\Http\JsonResponse

     */

    public function getMonthlyHomeWorkResult()

    {



        $previous_days = Carbon::now()->subDays(29)->format('Y-m-d h:i');



        $get_assign_student = TeacherEnroll::where('teacher_id', auth()->user()->id)->where('status', '1')->pluck('student_id')->toArray();



        $sql = StudentExam::where('is_submit', 1)->where('start_time', '=', 0)->where('updated_at', '>=', $previous_days)

            ->whereIn('student_id', array_unique($get_assign_student))

            ->with(['student:id,first_name,Last_name', 'exam.course:id,level'])

            ->select('id','student_id','score','spend_time','exam_title','exam_id', 'updated_at', 'created_at');

        if ($this->request->keyword != '') {

            $sql = $this->searchFilter($sql);

        }

        // FIXME : Data resource or optimize.

        return $sql->orderBy('updated_at', 'DESC')->take($this->request->page*20)->get();



    }



    /**

     * @param $student_exam_id

     */

    public function sendCert($student_exam_id)

    {

        //student_exam_id

        $studentIndAnswer = StudentExam::with('exam.lesson', 'student.parent.company')->find($student_exam_id);

        $end_course = Str::contains($studentIndAnswer->exam->lesson->title, ['End of Course']);

        if ($end_course == 1) {

            $parent_emails = collect();

            foreach ($studentIndAnswer->student->parent as $item) {

                $parent_emails->push($item);

            }

            $cal_score = $studentIndAnswer->score;

            $data['name'] = $studentIndAnswer->student->full_name;

            $data["email"] = $studentIndAnswer->student->email;

            $data["course"] = $studentIndAnswer->exam->course->title;

            $data["marks"] = $cal_score;

            $data["exam_date"] = date('F jS, Y ', strtotime($studentIndAnswer->created_at));

            $data["company_name"] = $studentIndAnswer->student->company->name;



            $pdf_name = Str::random(7) . '.pdf';

            $link = url('/storage/pdf/' . $pdf_name);

             $qr_code = \QrCode::size(200)
                ->backgroundColor(227,231,238)
                ->errorCorrection('H')
                ->generate($link);
            // $qr_code = '';

            $qr_file_name = Str::random(10) . '.png';

            Storage::put('public/qr/' . $qr_file_name, $qr_code);

            $data['qr_path'] = url('/storage/qr/' . $qr_file_name);

            $pdf = PDF::loadView('emails.Certificate2', $data, ['format' => 'A5-L']);



            Storage::put('public/pdf/' . $pdf_name, $pdf->output());

            //  live

            $path = url('/storage/pdf/' . $pdf_name);

            if (isset($parent_emails)) {

                foreach ($parent_emails as $mail) {

                    Mail::send([], [], function ($message) use ($data, $pdf, $path, $mail) {

                        $message->to($mail->email)

                            ->subject('CELT Certificate')

                            ->attach($path);

                    });

                };

            }

            if ($studentIndAnswer->student->send_email_status == 1) {

                Mail::send([], [], function ($message) use ($data, $pdf, $path) {

                    $message->to($data["email"], $data["email"])

                        ->subject('CELT Certificate')

                        ->attach($path);

                });

            }

            Advanced::insert([

                'student_id' => $studentIndAnswer->student->id,

                'exam_id' => $studentIndAnswer->id,

                'date' => $studentIndAnswer->created_at,

                'certificate' => $pdf_name,

                'course_type' => $studentIndAnswer->exam->course->title,

                'score' => $studentIndAnswer->score,

            ]);



        }

    }


    // manually generate certificate

    public function sendCertManual($student_exam_id, $date, $score)
    {
        //student_exam_id
        $studentIndAnswer = StudentExam::with('exam.lesson', 'student.parent.company')->find($student_exam_id);



        ///////////////////////////////////////////
     

            $parent_emails = collect();



            foreach ($studentIndAnswer->student->parent as $item) {
                $parent_emails->push($item);
            }



            $cal_score = $score;

            $data['name'] = $studentIndAnswer->student->full_name;

            $data["email"] = $studentIndAnswer->student->email;

            $data["course"] = $studentIndAnswer->exam->lesson->title;

            $data["marks"] = $cal_score;

            $data["exam_date"] = date('F jS, Y ', strtotime($date));

            $data["company_name"] = $studentIndAnswer->student->company->name;


            $pdf_name = Str::random(7) . '.pdf';         
             
            $link = url('/storage/pdf/' . $pdf_name);
        
               /////////////////////////////////////// PROBLEM
            $qr_code = \QrCode::size(200)
                ->backgroundColor(227,231,238)
                ->errorCorrection('H')
                ->generate($link);
            // $qr_code = '';
            $qr_file_name = Str::random(10) . '.png';
            /////////////////////////////////////////////////////////////////////////////
            Storage::put('public/qr/' . $qr_file_name, $qr_code);

            $data['qr_path'] = url('/storage/qr/' . $qr_file_name);
             
            $pdf = PDF::loadView('emails.Certificate2', $data, ['format' => 'A5-L']);
           

            Storage::put('public/pdf/' . $pdf_name, $pdf->output());
            //  live
            $path = url('/storage/pdf/' . $pdf_name);

            if (isset($parent_emails)) {

                foreach ($parent_emails as $mail) {

                    Mail::send([], [], function ($message) use ($data, $pdf, $path, $mail) {
                        $message->to($mail->email)
                            ->subject('CELT Certificate')
                            ->attach($path);
                    });

                };

            }

            if ($studentIndAnswer->student->send_email_status == 1) {
                Mail::send([], [], function ($message) use ($data, $pdf, $path) {
                    $message->to($data["email"], $data["email"])
                        ->subject('CELT Certificate')
                        ->attach($path);
                });
            }
            Advanced::insert([
                'student_id' => $studentIndAnswer->student->id,
                'exam_id' => $studentIndAnswer->id,
                'date' => $date,
                'certificate' => $pdf_name,
                'course_type' => $studentIndAnswer->exam->course->title,
                'score' => $studentIndAnswer->score,
            ]);

        


        return $link;
        /////////////////////////////////////////////////
    }



}

