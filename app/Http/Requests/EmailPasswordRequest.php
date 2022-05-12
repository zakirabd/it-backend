<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmailPasswordRequest extends FormRequest
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
            'type' => 'required|in:email,password',
        ];

        if ($this->type == 'email') {
            $validate['email'] = 'required|email|max:255|confirmed|unique:users';
        } else {
            $validate['new_password'] = 'required|string|min:8|max:255|confirmed';
        }

        return $validate;
    }
}
