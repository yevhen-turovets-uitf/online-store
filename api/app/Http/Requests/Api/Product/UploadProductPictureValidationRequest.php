<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Product;

use App\Http\Requests\Api\ApiFormRequest;
use Illuminate\Validation\Rules\File;

/**
 * @OA\Schema(
 *      description="Upload or update product picture request body data",
 *      schema="UploadProductPictureRequest",
 *      type="object",
 *      required={"image"},
 *      @OA\Xml(name="UploadProductPictureRequest"),
 *      @OA\Property(property="image", type="string", format="base64", description="Image file", example="data:image/jpeg;base64, yourSuperLongStringBinary"),
 * ),
 */


final class UploadProductPictureValidationRequest extends ApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'image' => [
                'required',
                'image',
                File::types(['jpg','jpeg','png'])->max(10240),
            ],
        ];
    }
}
