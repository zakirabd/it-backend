<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExamRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'course_id' => 'required|integer|exists:courses,id',
            'lesson_id' => 'required|integer|exists:lessons,id',
            'duration_minutes' => 'integer|min:0',
            'retake_minutes' => 'integer|min:0',
            'retake_time' => 'integer|min:0',
            'points' => 'integer|min:0',
        ];
    }
}
