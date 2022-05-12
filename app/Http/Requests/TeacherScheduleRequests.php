<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TeacherScheduleRequests extends FormRequest
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


            'teacher_id' => 'nullable|string|max:255',

            'group_id' => 'nullable|string|max:255',

            'student_id' => 'nullable|string|max:255',

            'study_mode' => 'nullable|string|max:255',

            'time' => 'nullable|string|max:255',

            'weekday' => 'nullable|string|max:255',

            'start_time' => 'nullable|string|max:255',

            'finish_time' => 'nullable|string|max:255',
        ];

         return $validate;
    }
}
