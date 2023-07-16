<?php

namespace App\Http\Controllers\Api\Variant;

use App\Http\Controllers\Api\ApiController;
use App\Http\Presenters\VariantSizeArrayPresenter;
use App\Http\Requests\Api\Variant\AddSizeValidationRequest;
use App\Http\Requests\Api\Variant\UpdateSizeValidationRequest;
use App\Models\VariantSize;
use Illuminate\Http\JsonResponse;

class VariantSizeController extends ApiController
{
    /**
     * @OA\Get(
     *      path="/sizes",
     *      summary="Get list of sizes",
     *      description="Return list of sizes",
     *      operationId="getSizesList",
     *      tags={"Sizes"},
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/VariantSizeResponse"))
     *       ),
     * )
     *
     * @param  VariantSizeArrayPresenter  $presenter
     *
     * @return JsonResponse
     */
    public function index(VariantSizeArrayPresenter $presenter): JsonResponse
    {
        $sizes = VariantSize::all();

        return $this->successResponse($presenter->presentCollection($sizes));
    }

    /**
     * @OA\Post(
     *      path="/sizes",
     *      summary="Store new size",
     *      description="Returns new size data",
     *      operationId="storeSize",
     *      tags={"Sizes"},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="name", type="string", description="Unique size name", example="XXS")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/VariantSizeResponse")
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The name has already been taken."),
     *              @OA\Property(
     *                  property="errors",
     *                  type="object",
     *                  @OA\Property(
     *                      property="name",
     *                      type="array",
     *                      @OA\Items(example={"The name field is required.","The name has already been taken."})
     *                  )
     *              )
     *          )
     *      )
     * )
     *
     * @param  AddSizeValidationRequest  $request
     * @param  VariantSizeArrayPresenter  $presenter
     *
     * @return JsonResponse
     */
    public function store(AddSizeValidationRequest $request,VariantSizeArrayPresenter $presenter): JsonResponse
    {
        $size = VariantSize::query()->create([
            'name' => $request->get('name'),
        ]);

        return $this->successResponse($presenter->present($size));
    }

    /**
     * @OA\Get(
     *      path="/sizes/{size}",
     *      summary="Get size information",
     *      description="Get size information by id",
     *      operationId="getSizeByID",
     *      tags={"Sizes"},
     *      @OA\Parameter(
     *          description="Size id",
     *          in="path",
     *          name="size",
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
     *          @OA\JsonContent(ref="#/components/schemas/VariantSizeResponse")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      ),
     * )
     *
     * @param  VariantSize  $size
     * @param  VariantSizeArrayPresenter  $presenter
     *
     * @return JsonResponse
     */
    public function show(VariantSize $size, VariantSizeArrayPresenter $presenter): JsonResponse
    {
        return $this->successResponse($presenter->present($size));
    }

    /**
     * @OA\Put(
     *      path="/sizes/{size}",
     *      summary="Update existing size",
     *      description="Returns updated size data",
     *      operationId="UpdateSize",
     *      tags={"Sizes"},
     *      @OA\Parameter(
     *          description="Size id",
     *          in="path",
     *          name="size",
     *          required=true,
     *          example="1",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="name", type="string", description="Unique size name", example="XXS")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/VariantSizeResponse")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The name has already been taken."),
     *              @OA\Property(
     *                  property="errors",
     *                  type="object",
     *                  @OA\Property(
     *                      property="name",
     *                      type="array",
     *                      @OA\Items(example={"The name field is required.","The name has already been taken."})
     *                  )
     *              )
     *          )
     *      )
     * )
     *
     * @param  UpdateSizeValidationRequest  $request
     * @param  VariantSize  $size
     * @param  VariantSizeArrayPresenter  $presenter
     *
     * @return JsonResponse
     */
    public function update(UpdateSizeValidationRequest $request, VariantSize $size, VariantSizeArrayPresenter $presenter): JsonResponse
    {
        $size->update([
            'name' => $request->get('name')
        ]);

        return $this->successResponse($presenter->present($size));
    }

    /**
     * @OA\Delete(
     *      path="/sizes/{size}",
     *      summary="Delete existing size",
     *      description="Deletes a record and returns no content",
     *      operationId="deleteSize",
     *      tags={"Sizes"},
     *      @OA\Parameter(
     *          description="Size id",
     *          in="path",
     *          name="size",
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
     * @param  VariantSize  $size
     *
     * @return JsonResponse
     */
    public function destroy(VariantSize $size): JsonResponse
    {
        $size->delete();

        return $this->emptyResponse();
    }
}
