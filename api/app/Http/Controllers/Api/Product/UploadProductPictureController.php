<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Api\ApiController;
use App\Http\Presenters\ProductArrayPresenter;
use App\Http\Requests\Api\Product\UploadProductPictureValidationRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadProductPictureController extends ApiController
{
    /**
     * @OA\Post(
     *      path="/products/{product}/picture",
     *      summary="Upload product picture",
     *      description="Returns updated product data",
     *      operationId="uploadProductPicture",
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
     *          @OA\JsonContent(ref="#/components/schemas/UploadProductPictureRequest")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/ProductResponse")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Resource Not Found"
     *      )
     * )
     *
     * @param  UploadProductPictureValidationRequest  $request
     * @param  Product  $product
     * @param  ProductArrayPresenter  $presenter
     *
     * @return JsonResponse
     */
    public function __invoke(
        UploadProductPictureValidationRequest $request,
        Product $product,
        ProductArrayPresenter $presenter
    ): JsonResponse
    {
        $file = $request->file('image');

        if ($product->getPicture()) {
            Storage::disk('s3')->delete(
                Str::remove(
                    env('AWS_LINK'),
                    $product->getPicture()
                )
            );
        }

        $path = Storage::disk('s3')->putFileAs(
            config('filesystems.product_pictures'),
            $file,
            $file->hashName(),
            's3'
        );

        $product->picture = Storage::disk('s3')->url($path);
        $product->save();

        return $this->successResponse($presenter->present($product));
    }
}
