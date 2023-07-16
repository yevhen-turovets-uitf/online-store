<?php

namespace App\Http\Controllers\Api\ProductVariant;

use App\Http\Controllers\Api\ApiController;
use App\Http\Presenters\ProductVariantArrayPresenter;
use App\Http\Requests\Api\ProductVariant\AddProductVariantValidationRequest;
use App\Http\Requests\Api\ProductVariant\UpdateProductVariantValidationRequest;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ProductVariantController extends ApiController
{
    /**
     * @OA\Get(
     *      path="/products/{product}/variants",
     *      summary="Get list product variant by product id",
     *      description="Return product variant list for product",
     *      operationId="getVariantListByProductId",
     *      tags={"Variants"},
     *      @OA\Parameter(
     *          description="Product id",
     *          in="path",
     *          name="product",
     *          required=true,
     *          example="1",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/ProductVariantResponse"))
     *       ),
     *      @OA\Response(
     *          response=404,
     *          description="Product Not Found",
     *       ),
     * )
     *
     * @param  Product  $product
     * @param  ProductVariantArrayPresenter  $presenter
     *
     * @return JsonResponse
     */
    public function index(Product $product, ProductVariantArrayPresenter $presenter): JsonResponse
    {
        $variants = $product->variants()->get();

        return $this->successResponse($presenter->presentCollection($variants));
    }

    /**
     * @OA\Post(
     *      path="/products/{product}/variants",
     *      summary="Store new product variant for product",
     *      description="Return new product variant for product",
     *      operationId="storeProductVariantForProduct",
     *      tags={"Variants"},
     *      @OA\Parameter(
     *          description="Product id",
     *          in="path",
     *          name="product",
     *          required=true,
     *          example="1",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/StoreProductVariantRequest")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/ProductVariantResponse")
     *       ),
     *      @OA\Response(
     *          response=404,
     *          description="Product Not Found",
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation errors",
     *       ),
     * )
     *
     * @param  AddProductVariantValidationRequest  $request
     * @param  Product  $product
     * @param  ProductVariantArrayPresenter  $presenter
     *
     * @return JsonResponse
     */
    public function store(AddProductVariantValidationRequest $request, Product $product, ProductVariantArrayPresenter $presenter): JsonResponse
    {
        $variantData = [
            'title' => $request->get('title'),
            'slug' => Str::slug($request->get('title')),
            'price' => $request->get('price'),
            'product_id' => $product->getId(),
            'size_id' => $request->get('size'),
        ];

        if($request->get('discountPrice'))
        {
            $variantData['discount_price'] = $request->get('discountPrice');
        }

        if($request->get('currency'))
        {
            $variantData['currency'] = $request->get('currency');
        }

        if($request->get('qty'))
        {
            $variantData['qty'] = $request->get('qty');
        }

        $variant = ProductVariant::query()->create($variantData);

        return $this->successResponse($presenter->present($variant->refresh()));
    }

    /**
     * @OA\Put(
     *      path="/variants/{variant}",
     *      summary="Update existing product variant",
     *      description="Returns updated product variant data",
     *      operationId="updateProductVariant",
     *      tags={"Variants"},
     *      @OA\Parameter(
     *          description="Product variant id",
     *          in="path",
     *          name="variant",
     *          required=true,
     *          example="1",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/UpdateProductVariantRequest")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/ProductVariantResponse")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation errors"
     *      )
     * )
     *
     * @param  UpdateProductVariantValidationRequest  $request
     * @param  ProductVariant  $variant
     * @param  ProductVariantArrayPresenter  $presenter
     *
     * @return JsonResponse
     */
    public function update(UpdateProductVariantValidationRequest $request, ProductVariant $variant, ProductVariantArrayPresenter $presenter): JsonResponse
    {
        $variant->update([
            'title' => $request->get('title'),
            'price' => $request->get('price'),
            'discount_price' => $request->get('discountPrice') ?? $variant->getDiscountPrice(),
            'currency' => $request->get('currency') ?? $variant->getCurrency(),
            'qty' => $request->get('qty') ?? $variant->getQty(),
            'size_id' => $request->get('size') ?? $variant->size->getId(),
        ]);

        return $this->successResponse($presenter->present($variant->refresh()));
    }

    /**
     * @OA\Delete(
     *      path="/variants/{variant}",
     *      summary="Delete existing product variant",
     *      description="Deletes a record and returns no content",
     *      operationId="deleteProductVariant",
     *      tags={"Variants"},
     *      @OA\Parameter(
     *          description="Product variant id",
     *          in="path",
     *          name="variant",
     *          required=true,
     *          example="1",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="No Content",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     *
     * @param  ProductVariant  $variant
     *
     * @return JsonResponse
     */
    public function destroy(ProductVariant $variant): JsonResponse
    {
        $variant->delete();

        return $this->emptyResponse();
    }

    /**
     * @OA\Get(
     *      path="/variants/{variant}",
     *      summary="Get product variant information",
     *      description="Get product variant information by id",
     *      operationId="getProductVariantByID",
     *      tags={"Variants"},
     *      @OA\Parameter(
     *          description="Product variant id",
     *          in="path",
     *          name="variant",
     *          required=true,
     *          example="1",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/ProductVariantResponse")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     *
     * @param  ProductVariant  $variant
     * @param  ProductVariantArrayPresenter  $presenter
     *
     * @return JsonResponse
     */
    public function show(ProductVariant $variant, ProductVariantArrayPresenter $presenter): JsonResponse
    {
        return $this->successResponse($presenter->present($variant));
    }
}
