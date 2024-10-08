<?php

namespace App\Http\Requests\NewsCategories;

use Illuminate\Foundation\Http\FormRequest;

class CreateClientTypeRequest extends FormRequest
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
            'name' => 'required:string|unique:client_types,name',
            'displayname' => 'required:string',
        ];

    }
}
