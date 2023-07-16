<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Api\ApiController;
use App\Http\Presenters\ProductArrayPresenter;
use App\Http\Requests\Api\Product\AddProductValidationRequest;
use App\Http\Requests\Api\Product\UpdateProductValidationRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ProductController extends ApiController
{
    /**
     * @OA\Get(
     *      path="/products",
     *      summary="Get list of products",
     *      description="Return list of products",
     *      operationId="getProductsList",
     *      tags={"Products"},
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/ProductResponse"))
     *       ),
     * )
     *
     * @param  ProductArrayPresenter  $presenter
     *
     * @return JsonResponse
     */
    public function index(ProductArrayPresenter $presenter): JsonResponse
    {
        $products = Product::all();

        return $this->successResponse($presenter->presentCollection($products));
    }

    /**
     * @OA\Post(
     *      path="/products",
     *      summary="Store new product",
     *      description="Returns new product data",
     *      operationId="storeProduct",
     *      tags={"Products"},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/StoreProductRequest")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/ProductResponse")
     *      )
     * )
     *
     * @param  AddProductValidationRequest  $request
     * @param  ProductArrayPresenter        $presenter
     *
     * @return JsonResponse
     */
    public function store(AddProductValidationRequest $request, ProductArrayPresenter $presenter): JsonResponse
    {
        $productData = [
            'title' => $request->get('title'),
            'slug' => Str::slug($request->get('title')),
            'price' => $request->get('price'),
        ];

        if ($request->get('discountPrice'))
        {
            $productData['discount_price'] = $request->get('discountPrice');
        }

        if ($request->get('currency'))
        {
            $productData['currency'] = $request->get('currency');
        }

        if ($request->get('qty'))
        {
            $productData['qty'] = $request->get('qty');
        }

        $product = Product::query()->create($productData);

        return $this->successResponse($presenter->present($product->refresh()));
    }

    /**
     * @OA\Get(
     *      path="/products/{product}",
     *      summary="Get product information",
     *      description="Get product information by id",
     *      operationId="getProductByID",
     *      tags={"Products"},
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
     *          @OA\JsonContent(ref="#/components/schemas/ProductResponse")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     *
     * @param  Product                $product
     * @param  ProductArrayPresenter  $presenter
     *
     * @return JsonResponse
     */
    public function show(Product $product, ProductArrayPresenter $presenter): JsonResponse
    {
        return $this->successResponse($presenter->present($product));
    }

    /**
     * @OA\Put(
     *      path="/products/{product}",
     *      summary="Update existing product",
     *      description="Returns updated product data",
     *      operationId="updateProduct",
     *      tags={"Products"},
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
     *          @OA\JsonContent(ref="#/components/schemas/UpdateProductRequest")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/ProductResponse")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     *
     * @param  UpdateProductValidationRequest  $request
     * @param  Product                         $product
     * @param  ProductArrayPresenter           $presenter
     *
     * @return JsonResponse
     */
    public function update(
        UpdateProductValidationRequest $request,
        Product $product,
        ProductArrayPresenter $presenter
    ): JsonResponse{
        $product->update(
            [
                'title' => $request->get('title'),
                'price' => $request->get('price'),
                'discount' => $request->get('discountPrice') ?? $product->getDiscountPrice(),
                'currency' => $request->get('currency') ?? $product->getCurrency(),
                'qty' => $request->get('qty') ?? $product->getQty(),
            ]
        );
        $product->save();

        return $this->successResponse($presenter->present($product));
    }

    /**
     * @OA\Delete(
     *      path="/products/{product}",
     *      summary="Delete existing product",
     *      description="Deletes a record and returns no content",
     *      operationId="deleteProduct",
     *      tags={"Products"},
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
     *          response=204,
     *          description="No Content",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     *
     * @param  Product  $product
     *
     * @return JsonResponse
     */
    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return $this->emptyResponse();
    }
}
