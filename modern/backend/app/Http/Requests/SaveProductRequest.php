<?php

namespace App\Http\Requests;

use App\Support\CatalogTypes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SaveProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can(CatalogTypes::module($this) === 'news' ? 'content.manage' : 'products.manage');
    }

    protected function prepareForValidation(): void
    {
        $definition = CatalogTypes::resolve($this, $this->route('product'));
        $this->merge(['type' => $definition['key']]);
        if (! $definition['features']['code'] && ! $this->filled('code')) {
            $this->merge(['code' => $this->route('product')?->code ?? 'AUTO-'.Str::uuid()]);
        }
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
        if (is_string($this->code)) {
            $this->merge(['code' => strtoupper(trim($this->code))]);
        }
    }

    public function rules(): array
    {
        $definition = CatalogTypes::all()[$this->input('type')];

        return [
            'type' => ['required', Rule::in(array_keys(CatalogTypes::all()))],
            'ai_seo' => ['nullable', 'array:vi,en'],
            'ai_seo.*' => ['array:summary,audience,use_cases,alternate_names,faqs,sources'],
            'ai_seo.*.summary' => ['nullable', 'string', 'max:1500'],
            'ai_seo.*.audience' => ['nullable', 'string', 'max:1000'],
            'ai_seo.*.use_cases' => ['nullable', 'string', 'max:2000'],
            'ai_seo.*.alternate_names' => ['nullable', 'string', 'max:1000'],
            'ai_seo.*.faqs' => ['array', 'list', 'max:20'],
            'ai_seo.*.faqs.*' => ['array:question,answer'],
            'ai_seo.*.faqs.*.question' => ['required', 'string', 'max:300'],
            'ai_seo.*.faqs.*.answer' => ['required', 'string', 'max:3000'],
            'ai_seo.*.sources' => ['array', 'list', 'max:10'],
            'ai_seo.*.sources.*' => ['array:title,url'],
            'ai_seo.*.sources.*.title' => ['required', 'string', 'max:255'],
            'ai_seo.*.sources.*.url' => ['required', 'url:http,https', 'max:2048'],
            'name' => ['required', 'string', 'max:255'], 'slug' => ['required', 'string', 'max:255', Rule::unique('products')->where('type', $this->input('type'))->ignore($this->route('product'))],
            'code' => ['required', 'string', 'max:100', Rule::unique('products')->where('type', $this->input('type'))->ignore($this->route('product'))],
            'category_id' => ['nullable', 'integer', Rule::exists('product_categories', 'id')->where('kind', 'category')->where('type', $this->input('type'))],
            'brand_id' => ['nullable', 'integer', Rule::exists('product_categories', 'id')->where('kind', 'brand')->where('type', $this->input('type'))],
            'size_ids' => ['present', 'array', 'max:100'], 'size_ids.*' => ['integer', 'distinct', Rule::exists('product_categories', 'id')->where('kind', 'size')->where('type', $this->input('type'))],
            'main_image_id' => ['nullable', 'integer', 'exists:product_media,id'], 'image_alt' => ['nullable', 'string', 'max:255'],
            'regular_price' => ['required', 'numeric', 'decimal:0,2', 'min:0', 'max:9999999999999.99'],
            'sale_price' => ['nullable', 'numeric', 'decimal:0,2', 'min:0', 'lte:regular_price'],
            'availability' => ['required', Rule::in(['in_stock', 'out_of_stock', 'preorder'])], 'rating' => ['nullable', 'numeric', 'between:1,5'],
            'description' => ['nullable', 'string', 'max:100000'], 'content' => ['nullable', 'string', 'max:500000'], 'specifications' => ['nullable', 'string', 'max:100000'],
            'seo_title' => ['nullable', 'string', 'max:255'], 'seo_description' => ['nullable', 'string', 'max:1000'], 'seo_keywords' => ['nullable', 'string', 'max:500'],
            'canonical_url' => ['nullable', 'url:http,https', 'max:2048'], 'noindex' => ['required', 'boolean'],
            'schema_mode' => ['required', Rule::in(['auto', 'custom', 'disabled'])], 'schema_data' => ['nullable', 'array'],
            'is_active' => ['required', 'boolean'], 'is_featured' => ['required', 'boolean'], 'is_new' => ['required', 'boolean'], 'is_bestseller' => ['required', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:1000000'],
            'images' => ['present', 'array', 'max:50'], 'images.*' => ['array:media_id,alt,caption,is_active'],
            'images.*.media_id' => ['required', 'integer', 'distinct', 'exists:product_media,id'], 'images.*.alt' => ['nullable', 'string', 'max:255'],
            'images.*.caption' => ['nullable', 'string', 'max:255'], 'images.*.is_active' => ['required', 'boolean'],
            'translations.en.slug' => ['nullable', 'string', 'max:255', Rule::unique('products', 'translations->en->slug')->where('type', $this->input('type'))->ignore($this->route('product'))],
            'translations' => ['nullable', 'array:en'], 'translations.en' => ['array:name,slug,description,content,specifications,seo_title,seo_description,seo_keywords'],
            'translations.en.name' => ['nullable', 'string', 'max:255'], 'translations.en.description' => ['nullable', 'string', 'max:100000'],
            'translations.en.content' => ['nullable', 'string', 'max:500000'], 'translations.en.specifications' => ['nullable', 'string', 'max:100000'],
            'translations.en.seo_title' => ['nullable', 'string', 'max:255'], 'translations.en.seo_description' => ['nullable', 'string', 'max:1000'], 'translations.en.seo_keywords' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($this->schema_mode === 'custom') {
                $schema = $this->input('schema_data');
                if (! is_array($schema) || ($schema['@context'] ?? null) !== 'https://schema.org' || ($schema['@type'] ?? null) !== 'Product' || strlen(json_encode($schema)) > 100000) {
                    $validator->errors()->add('schema_data', 'Schema cần là JSON Product có @context https://schema.org, tối đa 100 KB; không nhập thẻ script.');
                }
            }
        }];
    }
}
