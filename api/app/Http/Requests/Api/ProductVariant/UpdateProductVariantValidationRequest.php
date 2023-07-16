<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\ProductVariant;

use App\Http\Requests\Api\ApiFormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *      description="Update Product variant request body data",
 *      schema="UpdateProductVariantRequest",
 *      type="object",
 *      required={"title","price","size"},
 *      @OA\Xml(name="UpdateProductVariantRequest"),
 *      @OA\Property(property="title", type="string", description="Unique product variant title", example="pants"),
 *      @OA\Property(property="price", type="integer", format="int64", minimum="1", example="200"),
 *      @OA\Property(property="discountPrice", type="integer", format="int64", minimum="0", example="0"),
 *      @OA\Property(property="currency", type="string", description="Price currency one of USD or EUR", example="USD"),
 *      @OA\Property(property="qty", type="integer", format="int64", minimum="0", description="Quantity product on storage", example="15"),
 *      @OA\Property(property="size", type="integer", format="int64", description="Size id", example="2"),
 * ),
 */

final class UpdateProductVariantValidationRequest extends ApiFormRequest
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
                'unique:product_variants,title,'.$this->segment(4),
                'string'
            ],
            'price' => [
                'required',
                'integer',
                'gt:1'
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
            'size' => [
                'integer',
                'exists:variant_sizes,id'
            ],
        ];
    }
}
