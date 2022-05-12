<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceManuallyStoreRequest extends FormRequest
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

        return [
            'event'       => 'required|string',
            'teacher_id'  => 'required|integer|exists:users,id',
            'student_id'  => 'required|integer|exists:users,id',
            'lesson_mode' => 'required|string',
            'today' => 'required|date',
        ];
    }
}
