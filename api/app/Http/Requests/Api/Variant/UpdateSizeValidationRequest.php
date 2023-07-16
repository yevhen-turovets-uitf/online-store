<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Variant;

use App\Http\Requests\Api\ApiFormRequest;

final class UpdateSizeValidationRequest extends ApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'unique:variant_sizes,name,'.$this->segment(4),
                'string'
            ],
        ];
    }
}
