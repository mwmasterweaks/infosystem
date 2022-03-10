<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\client_attachment;

class clientAttachmentRequest extends FormRequest
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
        $rules = client_attachment::VALIDATION_RULES;
        return $rules;

        //comment lang ni maybe future magamit ning yawaa ni.
        // if($this->getMethod() == 'POST') { //store //DAW
        //     $rules += ['password' => 'required'];//array concat // DAW
        // }
        // else{//update DAW
        //     $rules['email'][1] = 'unique:users,email,' . //modified daw ang email
        //     request()->route(param:'user')->id;// wala ko kabaw ani.
        // }
    }
}
