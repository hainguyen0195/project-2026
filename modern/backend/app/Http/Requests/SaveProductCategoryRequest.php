<?php

namespace App\Http\Requests;

use App\Support\CatalogTypes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SaveProductCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can(CatalogTypes::module($this) === 'news' ? 'content.manage' : 'products.manage');
    }

    protected function prepareForValidation(): void
    {
        $definition = CatalogTypes::resolve($this, $this->route('category'));
        $this->merge(['type' => $definition['key']]);
        $translations = $this->input('translations');
        if (is_array($translations) && isset($translations['en']) && is_array($translations['en'])) {
            $english = $translations['en'];
            $slug = $english['slug'] ?? null;
            $name = $english['name'] ?? null;
            if (is_string($slug) || ($slug === null && is_string($name))) {
                $source = is_string($slug) && trim($slug) !== '' ? $slug : (is_string($name) ? $name : '');
                $translations['en']['slug'] = Str::slug($source) ?: null;
                $this->merge(['translations' => $translations]);
            }
        }
        if (is_string($this->slug) || ($this->slug === null && is_string($this->name))) {
            $this->merge(['slug' => Str::slug($this->slug ?: $this->name)]);
        }
    }

    public function rules(): array
    {
        $definition = CatalogTypes::all()[$this->input('type')];

        return [
            'type' => ['required', Rule::in(array_keys(CatalogTypes::all()))],
            'kind' => ['required', Rule::in(CatalogTypes::kinds($definition))],
            'name' => ['required', 'string', 'max:255'], 'slug' => ['required', 'string', 'max:255', Rule::unique('product_categories')->where('type', $this->input('type'))->ignore($this->route('category'))],
            'parent_id' => ['nullable', 'integer', Rule::exists('product_categories', 'id')->where('kind', 'category')->where('type', $this->input('type'))],
            'image_id' => ['nullable', 'integer', 'exists:product_media,id'],
            'description' => ['nullable', 'string', 'max:10000'],
            'seo_title' => ['nullable', 'string', 'max:255'], 'seo_description' => ['nullable', 'string', 'max:1000'], 'seo_keywords' => ['nullable', 'string', 'max:500'],
            'is_active' => ['required', 'boolean'], 'is_featured' => ['required', 'boolean'], 'sort_order' => ['required', 'integer', 'min:0', 'max:1000000'],
            'translations.en.slug' => ['nullable', 'string', 'max:255', Rule::unique('product_categories', 'translations->en->slug')->where('type', $this->input('type'))->ignore($this->route('category'))],
            'translations' => ['nullable', 'array:en'], 'translations.en' => ['array:name,slug,description,seo_title,seo_description,seo_keywords'],
            'translations.en.*' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
