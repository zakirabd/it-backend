<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function () {
    Route::post('passwordReset', 'Auth\LoginController@passwordReset');
    Route::post('login', 'Auth\LoginController@login')->middleware('online-user');
    Route::post('app/register', 'Auth\LoginController@oneSignalRegister');
    Route::post('sendResetLink', 'Auth\LoginController@sendResetLink');
    Route::post('tokenValidation', 'Auth\LoginController@veififyToken');

    Route::middleware(['auth:api', 'online-user'])->group(function () {
        Route::get('user', function (Request $request) {
            return $request->user();
        });
        Route::post('logout', 'Auth\LoginController@logout');
        Route::post('users/resend/email', 'UserController@resendVerifyEmail');
        Route::put('users/email-password/update', 'UserController@emailPasswordUpdate');
        Route::get('users/{id}/courses', 'UserController@courses');
        Route::post('users/{user}/courses/{course}', 'UserController@courseAttachOrDetach');
        Route::post('users/{id}/payment', 'UserController@payment');
        Route::get('users/payment-by-parent', 'UserController@paymentByParent');
        Route::get('users/payment-log', 'UserController@paymentLog');
        Route::get('users/teachers', 'UserController@teachers');
        Route::get('users/attendances', 'UserController@attendances');
        Route::get('users/get-student-teachers', 'UserController@getStudentTeacher');
        Route::post('users-manual-lock/{user}', 'UserController@userManualLockStatus');
        Route::get('online-user', 'UserController@userOnlineStatus');
        Route::get('student-export', 'UserController@studentExport');
        Route::get('user-info', 'UserController@userInfo');
        Route::apiResource('users', 'UserController');
        Route::post('lock-unlock-teacher', 'UserController@lockUnlockTeacher');
        Route::get('allCompanies', 'CompanyController@allCompanies');
        Route::get('allCompanies-status', 'CompanyController@allCompaniesStatus');
        Route::get('companies/{id}/courses', 'CompanyController@courses');
        Route::get('teacher-manager-data', 'CompanyController@companySummary');
        Route::get('daily-company-status', 'CompanyController@dailyCompanyStatus');
        Route::get('ungraded-by-teachers', 'CompanyController@ungradedByTeachers');
        Route::get('type-of-student', 'CompanyController@typesOfStudents');
        Route::apiResource('companies', 'CompanyController');
        Route::get('exam-taken', 'CompanyController@getExamTaken');
        Route::get('student-enrollment', 'CompanyController@studentEnrollment');
        Route::apiResource('listenings', 'ListeningController');
        Route::apiResource('courses', 'CourseController');
        Route::get('courses/{id}/lessons', 'CourseController@lessons');
        Route::get('lessons/{id}/essays', 'LessonController@essays');
        Route::apiResource('lessons', 'LessonController');
        Route::get('lessonCourses', 'LessonController@lessonCourses');
        Route::get('getAllLesson', 'LessonController@getAllLesson');
        Route::apiResource('essays', 'EssayController');
        Route::get('getlessonByCourse/{id}', 'EssayController@getlessonByCourse');
        Route::get('getSpeakingbyCourse/{id}', 'SpeakingController@getSpeakingByCourse');
        Route::get('getAnswerbySpeking/{speking_id}/{user_id}', 'SpeakingController@getAnswerbySpeking');
        Route::apiResource('speakings', 'SpeakingController');
        Route::post('speakingsAnswer', 'SpeakingController@speakingAnswer');
        Route::put('speakingsAnswerUpdate/{id}', 'SpeakingController@speakingsAnswerUpdate');
        Route::post('speakingsAnswerReview', 'SpeakingController@speakingsAnswerReview');
        Route::post('questionReorder', 'QuestionController@questionReorder');
        Route::apiResource('resources', 'ResourceController');
        Route::apiResource('courseAssigns', 'CourseAssignController');
        Route::apiResource('exams', 'ExamController');
        Route::apiResource('studentgroups', 'StudentGroupController');
        Route::get('getGroups', 'StudentGroupController@getAllGroups');
        Route::apiResource('teacherEnrolls', 'TeacherEnrollController');
        Route::apiResource('advanceds', 'AdvancedController');
        Route::apiResource('to-dos', 'ToDoController');
        Route::post('to-dos/change', 'ToDoController@statusChange');
        Route::apiResource('assessments', 'AssessmentController');
        Route::get('/get-assessment-by-student', 'AssessmentController@getAssessmentbyStudent');
        Route::get('/company-wise-assessment', 'AssessmentController@companyWiseAssessment');
        Route::get('/assessment-ungraded', 'AssessmentController@assessmentUngraded');
        Route::put('examLockUnlock/{id}', 'ExamController@examLockUnlock');
        Route::apiResource('attendances', 'AttendanceController')->only('index', 'store');
        Route::get('check-teacher-class', 'AttendanceController@checkTeacherAvailableClasses');
        Route::get('get-teacher-class', 'AttendanceController@getTeacherAvailableClasses');
        Route::post('add-attendance-manually', 'AttendanceController@storeAttendanceManually');
        Route::get('payment', 'AttendanceController@payment');
        Route::get('getPaymentEvent', 'AttendanceController@getPaymentEvent');
        Route::get('getStudentClass', 'AttendanceController@getStudentClass');
        Route::get('setPayment', 'AttendanceController@setPayment');
        Route::post('single-Payment', 'AttendanceController@singlePayment');
        Route::get('attendances/report', 'AttendanceController@report');
        Route::get('/student-unlock', 'AttendanceController@studentUnlock');
        Route::apiResource('essay-answers', 'EssayAnswerController');
        Route::get('essays-ungraded', 'EssayAnswerController@essayUngraded');
        Route::apiResource('essay-reviews', 'EssayReviewController');
        Route::post('essay-reviews/mass-update', 'EssayReviewController@update');
        Route::apiResource('emails', 'EmailController')->only('index', 'store', 'destroy');
        Route::apiResource('expenditures', 'ExpenditureController');
        Route::apiResource('questions', 'QuestionController');
        Route::get('exam-part-score', 'StudentExamController@partScore');
        Route::get('examLockAfterExam', 'StudentExamController@examLockAfterExam');
        Route::get('examStatus', 'StudentExamController@examStatus');
        Route::apiResource('studentAnswer', 'StudentExamController');
        Route::get('getExamScore', 'StudentExamController@getDailyExamByCompany');
        Route::apiResource('teacher-attendance', 'TeacherAttendanceController');
        Route::apiResource('parent-review', 'ParentReviewController');
        Route::apiResource('parent-review-comment', 'ParentReviewCommentController');
        Route::apiResource('payment-note', 'PaymentNoteController');

        Route::get('teacher-manager-data-student-enrollment', 'CompanyController@teacherManagerStudentEnrollment');


        Route::post('student-form', 'UserController@storeForm');

        Route::post('student-form-update', 'UserController@updateForm');

        Route::get('student-form-data', 'UserController@getFormData');

        Route::apiResource('students-log', 'StudentsLogController');


        Route::apiResource('teacher-schedule', 'TeacherScheduleController');


        Route::get('manager-all-teacher-statistics', 'CompanyController@companyAllTeacherStatistics');

        Route::get('teacher-daily-controle', 'AttendanceController@getDailyControle');

        Route::apiResource('speaking-teacher-attendance', 'SpeakingTeacherAttendanceController');
        Route::apiResource('upload-image', 'ImagesController');

        Route::apiResource('teacher-bonus', 'TeacherBonusController');


        Route::post('send-holiday-email', 'UserController@sendHolidayMail');

        Route::apiResource('office-manager-bonus', 'OfficeManagerBonusController');
        Route::get('get-teacher-groups', 'StudentGroupController@getTeacherGroups');

        Route::apiResource('teacher-deposits', 'TeacherDepositsController');


        Route::get('teacher-salary', 'AttendanceController@getTeacerSalary');

        Route::put('teacher-enroll-active-passive/{id}', 'AttendanceController@teacherEnrollActivePassive');
        Route::post('teacher-group-active-passive', 'AttendanceController@teacherGroupActivePassive');
        Route::put('teacherEnrolls-update/{id}', 'TeacherEnrollController@changeStudentEnroll');
        Route::post('teacher-group-update', 'TeacherEnrollController@teacherGroupUpdate');
        Route::apiResource('bonuses', 'BonusesController');

        Route::get('company-head-lock-teacher', 'UserController@getCompanyHeadLockedTeacher');


        Route::get('check-plagiarism', 'EssayAnswerController@checkPlagiarism');

    });
});

    //Version V2
    Route::group(['namespace' => 'v2', 'prefix' => 'v2'], function(){

    Route::middleware(['auth:api', 'online-user'])->group(function () {
        // exam refactor with resources
        Route::get('student-exam/parent-question-ids/{exam}/{user}', 'StudentExamController@getStudentExamParentQuestion');
        Route::get('student-exam-questions/{student_exam_questions}', 'StudentExamController@getStudentExamQuestions');
        Route::get('student-exam', 'StudentExamController@StudentExam');
        Route::post('student-exam-save', 'StudentExamController@studentExamSave');
        Route::post('student-exam-submit', 'StudentExamController@studentExamSubmit');
        // Homework
        Route::post('student-homework-submit', 'StudentExamController@StudentHomeWorkSubmit');
        Route::get('student-homework', 'StudentExamController@StudentHomework');
        //exam result
        Route::get('student-exam-result/parent-ids/{student_exam}', 'StudentExamController@studentExamResultParentIds');
        Route::get('student-exam-result/part-detail/{student_exam_questions}', 'StudentExamController@studentExamResultPartDetail');

        // new api

         Route::get('student-exam-manual', 'StudentExamController@StudentManuallyExam');
         Route::post('student-exam-submit-manual', 'StudentExamController@studentExamManualSubmit');

    });
});
