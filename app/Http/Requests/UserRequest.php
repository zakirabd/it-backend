<?php



namespace App\Http\Requests;



use Illuminate\Foundation\Http\FormRequest;



class UserRequest extends FormRequest

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

            'first_name' => 'required|string|max:255',

            'last_name' => 'required|string|max:255',

            'email' => 'required|email|max:255|unique:users',

            'phone_number' => 'required|string|max:255',

            'password' => 'required|string|min:8|max:255',



            'avatar_url' => 'nullable|image',

            'parent' => 'nullable|string|regex:/^\d+(((,\d+)?,\d+)?,\d+)?$/',

            'role' => 'required|string|in:super_admin,chief_auditor,auditor,content_manager,content_master,office_manager,teacher_manager,accountant,company_head,speaking_teacher,head_teacher,teacher,student,parent',

        ];



        if ($this->isMethod('put')) {

            $validate['email'] = 'required|email|max:255|unique:users,email,' . $this->get('id');

            $validate['password'] = 'nullable|string|min:8|max:255';

            $validate['avatar_url'] = 'nullable|sometimes|image';

        }



        if ($this->input('role') == 'company_head') {

            $validate['company_id'] = 'required|integer|exists:companies,id';

        }



        return $validate;

    }

}

