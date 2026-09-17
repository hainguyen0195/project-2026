<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductCategory extends Model
{
    use HasFactory;

    protected $attributes = ['type' => 'san-pham'];

    protected $fillable = ['type', 'kind', 'parent_id', 'name', 'slug', 'image_id', 'description', 'seo_title', 'seo_description', 'seo_keywords', 'translations', 'is_active', 'is_featured', 'sort_order'];

    protected function casts(): array
    {
        return ['translations' => 'array', 'is_active' => 'boolean', 'is_featured' => 'boolean'];
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(ProductMedia::class, 'image_id');
    }
}
