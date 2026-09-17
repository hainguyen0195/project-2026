<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $attributes = ['type' => 'san-pham'];

    protected $fillable = ['type', 'name', 'slug', 'code', 'category_id', 'brand_id', 'main_image_id', 'image_alt', 'regular_price', 'sale_price', 'availability', 'rating', 'description', 'content', 'specifications', 'seo_title', 'seo_keywords', 'seo_description', 'canonical_url', 'noindex', 'schema_mode', 'schema_data', 'ai_seo', 'translations', 'is_active', 'is_featured', 'is_new', 'is_bestseller', 'sort_order'];

    protected function casts(): array
    {
        return ['regular_price' => 'decimal:2', 'sale_price' => 'decimal:2', 'rating' => 'decimal:1', 'translations' => 'array', 'schema_data' => 'array', 'ai_seo' => 'array', 'noindex' => 'boolean', 'is_active' => 'boolean', 'is_featured' => 'boolean', 'is_new' => 'boolean', 'is_bestseller' => 'boolean'];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'brand_id');
    }

    public function mainImage(): BelongsTo
    {
        return $this->belongsTo(ProductMedia::class, 'main_image_id');
    }

    public function sizes(): BelongsToMany
    {
        return $this->belongsToMany(ProductCategory::class, 'product_size');
    }

    public function images(): BelongsToMany
    {
        return $this->belongsToMany(ProductMedia::class, 'product_images', 'product_id', 'media_id')->withPivot(['alt', 'caption', 'sort_order', 'is_active'])->orderByPivot('sort_order');
    }
}
