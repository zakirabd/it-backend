<?php



namespace App\Http\Requests;



use Illuminate\Foundation\Http\FormRequest;



class AttendanceCheckRequest extends FormRequest

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



        $validate = [];



        if (auth()->user()->role != 'student' && auth()->user()->role != 'parent') {

            $validate['teacher_id'] = 'required|integer|exists:users,id';

            $validate['user_id'] = 'required|integer|exists:users,id';

        }



        return $validate;

    }



    /**

     * @return string[]

     */

    public function messages(): array

    {

        return [

            'teacher_id.required' => 'Please Select a teacher',

            'user_id.required'    => 'Student field is required',

        ];

    }

}

