<?php

namespace App\Http\Requests\SuccessStories;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSuccessStoriesRequest extends FormRequest
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
            'title' => [
                'required',
                'string',
                'unique:success_stories,title' . ($decryptedId ? ",$decryptedId" : ''),
            ],

        ];

    }
}
