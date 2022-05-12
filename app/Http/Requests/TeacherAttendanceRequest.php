<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TeacherAttendanceRequest extends FormRequest
{
    /**
     * @var mixed
     */
    private $class_type;
    /**
     * @var mixed
     */
    private $teacher_id;
    /**
     * @var mixed
     */
    private $company_id;
    /**
     * @var mixed
     */
    private $number_of_class;
    /**
     * @var mixed
     */
    private $status;

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
            'class_type' => 'required',
            'status' => 'required',
            'teacher_id' => 'required',
            'company_id' => 'required',
            'number_of_class' => 'required|integer',
        ];
    }
}
