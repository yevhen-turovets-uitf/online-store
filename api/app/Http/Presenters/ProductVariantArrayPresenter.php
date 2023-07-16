<?php

declare(strict_types=1);

namespace App\Http\Presenters;

use App\Contracts\PresenterCollectionInterface;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;

/**
 * @OA\Schema(
 *      description="Product variant array response",
 *      schema="ProductVariantResponse",
 *      type="object",
 *      @OA\Xml(name="ProductVariantArrayPresenter"),
 *      @OA\Property(property="id", type="integer", format="int64", example="2"),
 *      @OA\Property(property="title", type="string", description="Unique product variant title", example="T-shirt S"),
 *      @OA\Property(property="slug", type="string", description="Unique slug from product variant title", example="t-shirt-s2"),
 *      @OA\Property(property="price", type="integer", format="int64", minimum="1", example="300"),
 *      @OA\Property(property="discountPrice", type="integer", format="int64", minimum="0", example="0"),
 *      @OA\Property(property="currency", type="string", description="Currency of price one of USD or EUR", example="USD"),
 *      @OA\Property(property="qty", type="integer", format="int64", minimum="0", description="Quantity product variant on storage", example="10"),
 *      @OA\Property(property="size", type="object", ref="#/components/schemas/VariantSizeResponse"),
 *      @OA\Property(property="productId", type="integer", format="int64", description="Product's id of the product variant", example="1"),
 *      @OA\Property(property="sizeId", type="integer", format="int64", description="Size's id of the product variant", example="2"),
 * )
 */

final class ProductVariantArrayPresenter implements PresenterCollectionInterface
{
    public function __construct(private VariantSizeArrayPresenter $sizeArrayPresenter)
    {}

    public function present(ProductVariant $variant): array
    {
        return [
            'id' => $variant->getId(),
            'title' => $variant->getTitle(),
            'slug' => $variant->getSlug(),
            'price' => $variant->getPrice(),
            'discountPrice' => $variant->getDiscountPrice(),
            'currency' => $variant->getCurrency(),
            'qty' => $variant->getQty(),
            'size' => $this->sizeArrayPresenter->present($variant->size),
            'productId' => $variant->product->getId(),
            'sizeId' => $variant->size->getId(),
        ];
    }

    public function presentCollection(Collection $variants): array
    {
        return $variants->map(
            function (ProductVariant $variant) {
                return $this->present($variant);
            }
        )->all();
    }
}
