<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\ApiFormRequest;
use Illuminate\Validation\Rules\Password;

final class ResetPasswordValidationRequest extends ApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'token' => 'required',
            'email' => [
                'required',
                'email',
                'max:255',
                'exists:users,email',
                'regex:/(.*)+@([\w-]+\.)+[\w-]{2,4}$/'
            ],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers(),
                'string',
            ],
        ];
    }
}
