<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 *
 * @OA\Schema(
 * required={"title","password"},
 * @OA\Xml(name="Product"),
 * @OA\Property(property="id", type="integer", readOnly="true", example="1"),
 * @OA\Property(property="title", type="string", example="T-shirt"),
 * @OA\Property(property="slug", type="string", description="Unique slug from product title", example="t-shirt12"),
 * @OA\Property(property="picture", type="string", nullable="true", description="Product pictrue url", example="https://via.placeholder.com/640x480.png/0022ff?text=dolorem"),
 * @OA\Property(property="price", type="integer", format="int64", minimum="1", example="300"),
 * @OA\Property(property="discount_price", type="integer", format="int64", minimum="0", example="0"),
 * @OA\Property(property="currency", type="string", description="Price currency one of USD or EUR", example="USD"),
 * @OA\Property(property="qty", type="integer", format="int64", minimum="0", description="Quantity product on storage", example="10"),
 * @OA\Property(property="created_at", ref="#/components/schemas/BaseModel/properties/created_at"),
 * @OA\Property(property="updated_at", ref="#/components/schemas/BaseModel/properties/updated_at"),
 * )
 *
 * Class Product
 *
 * @package App/Models
 */
class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'picture',
        'price',
        'discount_price',
        'currency',
        'qty',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($product) {
            $product->slug = Str::slug($product->title). $product->id;
            $product->save();
        });

        static::updated(function ($product) {
            if($product->slug != Str::slug($product->getTitle()). $product->getId()){
                $product->slug = Str::slug($product->title). $product->id;
                $product->save();
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

    public function getPicture(): ?string
    {
        return $this->picture;
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

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }
}
