<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SpeakingRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'course_id' => 'required|int',
            'speaking_type' => 'required|string',
            'question' => 'required|string|max:500',
            'lesson_id' => 'required|int',
        ];
        return $validate;

    }
}
