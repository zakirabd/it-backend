<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentsLogRequests extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
         return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $validate = [


            'student_id' => 'required|string|max:255',

            'type' => 'required|string|max:255',

            'enroll_type' => 'required|string|max:255',


            'course_id' => 'nullable|string|max:255',


            'teacher_id' => 'nullable|string|max:255',


            'lesson_mode' => 'nullable|email|max:255',


            'study_mode' => 'nullable|email|max:255',
        ];
        return $validate;
    }
}
