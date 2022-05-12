<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuestionRequest extends FormRequest
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
            'type' => 'required',
        ];

        if ($this->request->get('type') != 'parent') {
            $validate['score'] = 'required';
            if ($this->request->get('sub_type') != 'image_to_image' &&

                $this->request->get('sub_type') != 'text_to_image' &&
                $this->request->get('type') != 'single_image_choice' &&

                $this->request->get('type') != 'single_audio_with_image' &&
                $this->request->get('type') != 'dropdown_question_type'

            ) {

                if (!empty(json_decode($this->request->get('question_option'), true))) {
                    $check=0;
                    foreach (json_decode($this->request->get('question_option'), true) as $key => $val) {
                        if (empty($val['title'])) {
                            $validate['answer-'. ($key+1)] = 'required';
                        }
                        if ($val['check']){
                            $check++;
                        }
                    }
                    if ($check==0){
                        $validate['correct'] = 'required';
                    }
                }

            }

        }
        return $validate;

    }
}
