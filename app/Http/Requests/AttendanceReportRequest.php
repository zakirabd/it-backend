<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceReportRequest extends FormRequest
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
        $validate   = [];
        $company_id = ['chief_auditor', 'auditor', 'teacher_manager', 'accountant'];
        $teacher_id = ['company_head', 'office_manager', 'parent'];
        if (in_array(auth()->user()->role, $company_id)) {
            $validate['company_id'] = 'required|exists:users,company_id';
            $validate['teacher_id'] = 'required|exists:users,id';
        }
        if (in_array(auth()->user()->role, $teacher_id)) {
            $validate['teacher_id'] = 'required|integer|exists:users,id';
        }

        return $validate;
    }

    /**
     * @return string[]
     */
    public function messages()
    {
        return [
            'teacher_id.required' => 'Please Select a Teacher',
            'company_id.required' => 'Please Select a Company',
        ];
    }
}
