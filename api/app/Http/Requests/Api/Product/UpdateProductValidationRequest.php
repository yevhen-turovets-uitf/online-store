<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Product;

use App\Http\Requests\Api\ApiFormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *      description="Update Product request body data",
 *      schema="UpdateProductRequest",
 *      type="object",
 *      required={"title","price"},
 *      @OA\Xml(name="UpdateProductRequest"),
 *      @OA\Property(property="title", type="string", description="Unique product title", example="pants"),
 *      @OA\Property(property="price", type="integer", format="int64", minimum="1", example="200"),
 *      @OA\Property(property="discountPrice", type="integer", format="int64", minimum="0", example="0"),
 *      @OA\Property(property="currency", type="string", description="Price currency one of USD or EUR", example="USD"),
 *      @OA\Property(property="qty", type="integer", format="int64", minimum="0", description="Quantity product on storage", example="15"),
 * ),
 */

final class UpdateProductValidationRequest extends ApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'unique:products,title,'.$this->segment(4),
                'string'
            ],
            'price' => [
                'required',
                'integer',
                'gt:0'
            ],
            'discountPrice' => [
                'integer',
                'gte:0',
            ],
            'currency' => [
                Rule::in(['USD','EUR']),
                'string'
            ],
            'qty' => [
                'integer',
                'gte:0'
            ],
        ];
    }
}
