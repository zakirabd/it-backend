<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResourceRequest extends FormRequest
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
            'assign_role' => 'required',
            'attachment' => 'required',
        ];
        if ($this->isMethod('put')) {
            $validate['attachment'] = 'nullable|sometimes';
        }

        return $validate;

    }
}
