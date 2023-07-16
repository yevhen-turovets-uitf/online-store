<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 *
 * @OA\Schema(
 * @OA\Xml(name="VariantSize"),
 * @OA\Property(property="id", type="integer", readOnly="true", example="1"),
 * @OA\Property(property="name", type="string", example="XL"),
 * )
 *
 * Class VariantSize
 *
 * @package App/Models
 */
class VariantSize extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function productVariant(): HasMany
    {
        return $this->hasMany(ProductVariant::class,'size_id');
    }
}
