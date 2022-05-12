<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseAssignRequest extends FormRequest
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
            'companie_id' =>'required|unique:course_assigns,companie_id,' . $this->id . ',id,course_id,' . $this->course_id,
            'course_id' => 'required',
        ];

        return $validate;

    }
}
