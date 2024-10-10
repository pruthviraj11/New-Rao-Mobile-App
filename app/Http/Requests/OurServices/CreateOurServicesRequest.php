<?php

namespace App\Http\Requests\OurServices;

use Illuminate\Foundation\Http\FormRequest;

class CreateOurServicesRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title' => 'required:string|unique:our_services,title',


        ];

    }
}
