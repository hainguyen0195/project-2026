<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        $data['size_ids'] = $this->sizes->modelKeys();
        $data['images'] = $this->images->map(fn ($media): array => ['media_id' => $media->id, 'url' => $media->url, 'alt' => $media->pivot->alt ?? '', 'caption' => $media->pivot->caption ?? '', 'is_active' => (bool) $media->pivot->is_active])->all();
        $data['discount_percent'] = $this->sale_price !== null && (float) $this->regular_price > 0 ? round((1 - (float) $this->sale_price / (float) $this->regular_price) * 100, 2) : 0;
        $data['resolved_schema'] = $this->schema_mode === 'disabled' ? null : ($this->schema_mode === 'custom' ? $this->schema_data : $this->automaticSchema());

        return $data;
    }

    private function automaticSchema(): array
    {
        $schema = ['@context' => 'https://schema.org', '@type' => 'Product', 'name' => $this->name, 'sku' => $this->code,
            'description' => trim(strip_tags($this->seo_description ?: $this->description ?? '')),
            'offers' => ['@type' => 'Offer', 'priceCurrency' => 'VND', 'price' => $this->sale_price ?? $this->regular_price,
                'availability' => 'https://schema.org/'.match ($this->availability) {
                    'out_of_stock' => 'OutOfStock', 'preorder' => 'PreOrder', default => 'InStock'
                }]];
        $images = $this->images->filter(fn ($media) => (bool) $media->pivot->is_active)->map(fn ($media) => $media->url)->all();
        if ($this->mainImage) {
            array_unshift($images, $this->mainImage->url);
        }
        if ($images !== []) {
            $schema['image'] = array_values(array_unique($images));
        }
        if ($this->brand) {
            $schema['brand'] = ['@type' => 'Brand', 'name' => $this->brand->name];
        }
        $aliases = array_values(array_unique(array_filter(array_map('trim', preg_split('/\R/u', $this->ai_seo['vi']['alternate_names'] ?? '')))));
        if ($aliases !== []) {
            $schema['alternateName'] = $aliases;
        }
        if ($this->canonical_url) {
            $schema['url'] = $this->canonical_url;
            $schema['offers']['url'] = $this->canonical_url;
        }

        return $schema;
    }
}
