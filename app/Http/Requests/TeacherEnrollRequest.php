<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TeacherEnrollRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $validate = [
            'study_mode' => 'required|string|max:255',
            'teacher_id' => 'required',
            'lesson_houre' => 'required',

            'lesson_houre' => 'required',
        ];
        return $validate;

    }
}
