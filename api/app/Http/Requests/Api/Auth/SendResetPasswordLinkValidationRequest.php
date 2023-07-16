<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\ApiFormRequest;

final class SendResetPasswordLinkValidationRequest extends ApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
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
