<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentFormRequests extends FormRequest
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


            'company_id' => 'required|string|max:255',


            'date' => 'nullable|string|max:255',


            'first_name' => 'required|string|max:255',


            'last_name' => 'required|string|max:255',


            'email' => 'nullable|email|max:255',


            'phone_number' => 'nullable|string|max:255',


            'date_of_birth' => 'nullable|string|max:255',


            'parent_first_name' => 'nullable|string|max:255',

            'parent_last_name' => 'nullable|string|max:255',

            'parent_date_of_birth' => 'nullable|string|max:255',

            'parent_email' => 'nullable|string|max:255',

            'current_education' => 'nullable|string|max:255',


            
            'education_center' => 'nullable|string|max:255',

            'class_course' => 'nullable|string|max:255',

            'faculty' => 'nullable|string|max:255',

            'specialty' => 'nullable|string|max:255',

            'gpa' => 'nullable|string|max:255',


            'language_certification' => 'nullable|string|max:255',

            'education_type' => 'nullable|string|max:255',

            'country' => 'nullable|string|max:255',

            'next_specialty' => 'nullable|string|max:255',

            'budget' => 'nullable|string|max:255',


            'source' => 'nullable|string|max:255',

            'other_source' => 'nullable|string|max:255',

            'education_financing' => 'nullable|string|max:255',


        ];
        return $validate;
    }
}
