<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ForgotRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'email' => [
                'required',
                'email',
                'max:255',
                'exists:users,email',
                'regex:/(.*)+@([\w-]+\.)+[\w-]{2,4}$/'
            ],
        ];
    }
}
