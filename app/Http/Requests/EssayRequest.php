<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EssayRequest extends FormRequest
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
            'essay_type' => 'required|string',
            'lesson_id' => 'required|int',
        ];
        $str_word_count = str_word_count($this->request->get('question'));
        if ($str_word_count > 501) {
            $validate['question'] = 'required|string|max:500';
        } else {
            $validate['question'] = 'required|string';
        }
        return $validate;

    }
}
