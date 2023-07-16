<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 *
 * @OA\Schema(
 * @OA\Xml(name="ProductVariant"),
 * @OA\Property(property="id", type="integer", readOnly="true", example="1"),
 * @OA\Property(property="title", type="string", example="T-shirt"),
 * @OA\Property(property="slug", type="string", description="Unique slug from product variant title", example="t-shirt12"),
 * @OA\Property(property="price", type="integer", format="int64", minimum="1", example="300"),
 * @OA\Property(property="discount_price", type="integer", format="int64", minimum="0", example="0"),
 * @OA\Property(property="currency", type="string", description="Price currency one of USD or EUR", example="USD"),
 * @OA\Property(property="qty", type="integer", format="int64", minimum="0", description="Quantity product on storage", example="10"),
 * @OA\Property(property="product_id", type="integer", format="int64", minimum="0", description="Product's id of the product variant", example="1"),
 * @OA\Property(property="size_id", type="integer", format="int64", minimum="0", description="Size's id of the product variant", example="1"),
 * @OA\Property(property="created_at", ref="#/components/schemas/BaseModel/properties/created_at"),
 * @OA\Property(property="updated_at", ref="#/components/schemas/BaseModel/properties/updated_at"),
 * )
 *
 * Class ProductVariant
 *
 * @package App/Models
 */
class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'price',
        'discount_price',
        'currency',
        'qty',
        'product_id',
        'size_id',
    ];

    protected $touches = ['product'];
    protected $with = ['size'];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($variant) {
            $variant->slug = Str::slug($variant->title). $variant->id;
            $variant->save();
        });

        static::updated(function ($variant) {
            if($variant->slug != Str::slug($variant->getTitle()).$variant->getId()){
                $variant->slug = Str::slug($variant->title). $variant->id;
                $variant->save();
            }
        });
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getDiscountPrice(): int
    {
        return $this->discount_price;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getQty(): int
    {
        return $this->qty;
    }

    public function size(): BelongsTo
    {
        return $this->belongsTo(VariantSize::class,'size_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class,'product_id');
    }
}
