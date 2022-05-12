<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdvancedRequest extends FormRequest
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
            'date' => 'required',
            'course_type' => 'required',
        ];
        if ($this->request->get('course_type')=='Study Abroad'){
            $validate['title']= 'required|string|max:255';
            $validate['program']= 'required';
            $validate['year']= 'required';
            $validate['scholarship']= 'required';
        }
        if ($this->request->get('course_type')!='Study Abroad'){
            $validate['score']= 'required';
        }
      if ($this->request->get('course_type')=='Other'){
            $validate['title']= 'required|string|max:255';
        }

        return $validate;

    }
}
