<?php

declare(strict_types=1);

namespace App\Http\Presenters;

use App\Contracts\PresenterCollectionInterface;
use App\Models\VariantSize;
use Illuminate\Support\Collection;

/**
 * @OA\Schema(
 *      schema="VariantSizeResponse",
 *      description="Size array response",
 *      type="object",
 *      @OA\Property(property="id", type="integer", format="int64", example="2"),
 *      @OA\Property(property="name", type="string", description="Size name", example="S"),
 * )
 */

final class VariantSizeArrayPresenter implements PresenterCollectionInterface
{
    public function present(VariantSize $size): array
    {
        return [
            'id' => $size->getId(),
            'name' => $size->getName(),
        ];
    }

    public function presentCollection(Collection $sizes): array
    {
        return $sizes->map(
            function (VariantSize $size) {
                return $this->present($size);
            }
        )->all();
    }
}
