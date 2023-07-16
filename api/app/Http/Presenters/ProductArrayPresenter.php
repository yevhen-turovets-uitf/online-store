<?php

declare(strict_types=1);

namespace App\Http\Presenters;

use App\Contracts\PresenterCollectionInterface;
use App\Models\Product;
use Illuminate\Support\Collection;

/**
 * @OA\Schema(
 *      schema="ProductResponse",
 *      description="Product array response",
 *      type="object",
 *      required={"title","price"},
 *      @OA\Xml(name="ProductArrayPresenter"),
 *      @OA\Property(property="id", type="integer", format="int64", description="Unique product title", example="4"),
 *      @OA\Property(property="title", type="string", description="Unique product title", example="T-shirt"),
 *      @OA\Property(property="slug", type="string", description="Unique slug from product title", example="t-shirt12"),
 *      @OA\Property(property="picture", type="string", nullable="true", description="Product pictrue url", example="https://via.placeholder.com/640x480.png/0022ff?text=dolorem"),
 *      @OA\Property(property="price", type="integer", format="int64", minimum="1", example="300"),
 *      @OA\Property(property="discountPrice", type="integer", format="int64", minimum="0", example="0"),
 *      @OA\Property(property="currency", type="string", description="Currency of price one of USD or EUR", example="USD"),
 *      @OA\Property(property="qty", type="integer", format="int64", minimum="0", description="Quantity product on storage", example="10"),
 *      @OA\Property(property="variants", type="array", @OA\Items(ref="#/components/schemas/ProductVariantResponse")),
 * ),
 */

final class ProductArrayPresenter implements PresenterCollectionInterface
{
    public function __construct(
        private ProductVariantArrayPresenter $variantArrayPresenter
    ){}

    public function present(Product $product): array
    {
        return [
            'id' => $product->getId(),
            'title' => $product->getTitle(),
            'slug' => $product->getSlug(),
            'picture' => $product->getPicture(),
            'price' => $product->getPrice(),
            'discountPrice' => $product->getDiscountPrice(),
            'currency' => $product->getCurrency(),
            'qty' => $product->getQty(),
            'variants' => $this->variantArrayPresenter->presentCollection($product->variants()->get()),
        ];
    }

    public function presentCollection(Collection $products): array
    {
        return $products->map(
            function (Product $product) {
                return $this->present($product);
            }
        )->all();
    }
}
