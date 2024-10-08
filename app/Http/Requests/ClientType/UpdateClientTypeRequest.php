<?php

namespace App\Http\Requests\ClientType;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClientTypeRequest extends FormRequest
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
        $decryptedId = decrypt($this->route('encrypted_id'));

        return [
            'name' => [
                'required',
                'string',
                'unique:client_types,name' . ($decryptedId ? ",$decryptedId" : ''),
            ],
            'displayname' => [
                'required',
                'string'
            ],
        ];

    }
}
