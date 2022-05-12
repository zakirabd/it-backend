<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListeningRequest extends FormRequest
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
            'audio_file' => $this->isMethod('put') ? '' : 'required|file',
            'course_id' => 'required|integer|exists:courses,id',
            'lesson_id' => 'required|integer|exists:lessons,id',
        ];
    }
}
