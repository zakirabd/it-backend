<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ParentReviewRequest extends FormRequest
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
            'note' => 'required|string',
            'rating'=>'required',
            'teacher_id' => 'required|integer|exists:users,id',

        ];
    }
    public function messages()
    {
        return [
            'teacher_id.required' => 'Teacher field is  required',
        ];
    }
}
