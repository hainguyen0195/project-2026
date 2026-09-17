<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductMedia;
use App\Models\SiteSetting;
use App\Support\ProductHtml;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class HomepageController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $request->validate(['locale' => ['sometimes', 'in:vi,en']]);
        $settings = SiteSetting::where('key', 'general')->first()?->data ?? [];
        $locale = $request->input('locale', $settings['options']['lang_default'] ?? 'vi');
        $translate = fn (array $translations, string $field): string => $translations[$locale][$field] ?? $translations['vi'][$field] ?? '';
        $media = SiteSetting::where('key', 'like', 'photo:%')->get()->filter(fn (SiteSetting $item): bool => (bool) ($item->data['is_active'] ?? false))->sortBy(fn (SiteSetting $item): int => $item->data['sort_order'] ?? 0);
        $images = ProductMedia::whereIn('id', $media->map(fn (SiteSetting $item) => $item->data['image_id'] ?? null))->get()->keyBy('id');
        $photos = [];
        foreach ($media as $item) {
            $type = explode(':', $item->key)[1] ?? '';
            if (! in_array($type, ['logo', 'logo-footer', 'favicon', 'slide', 'social', 'social-footer', 'dmca', 'bct'], true)) {
                continue;
            }
            $image = $images->get($item->data['image_id'] ?? null);
            if (! $image) {
                continue;
            }
            $translations = $item->data['translations'] ?? [];
            $photos[$type][] = ['id' => $item->id, 'image' => $this->image($image), 'name' => $translate($translations, 'name'), 'alt' => $translate($translations, 'alt'), 'text1' => $translate($translations, 'text1'), 'text2' => $translate($translations, 'text2'), 'link' => $this->safeUrl($item->data['link'] ?? '')];
        }
        $seo = Product::with('mainImage')->where('type', config('homepage.seo_type'))->first();
        $about = Product::with('mainImage')->where('type', config('homepage.about_type'))->where('is_active', true)->first();
        $products = Product::with('mainImage')->where('type', config('homepage.product_type'))->where('is_active', true)->orderByDesc('is_featured')->orderBy('sort_order')->orderByDesc('id')->limit(config('homepage.product_limit'))->get();
        $news = Product::with('mainImage')->where('type', config('homepage.news_type'))->where('is_active', true)->orderBy('sort_order')->orderByDesc('id')->limit(config('homepage.news_limit'))->get();
        $translations = $settings['translations'] ?? [];

        return response()->json(['data' => [
            'locale' => $locale,
            'company' => ['name' => $translate($translations, 'name'), 'slogan' => $translate($translations, 'slogan'), 'address' => $translate($translations, 'address'), 'copyright' => $translate($translations, 'copyright'), ...Arr::only($settings['options'] ?? [], ['email', 'hotline', 'phone', 'website', 'worktime'])],
            'media' => (object) $photos,
            'seo' => ['title' => $seo ? $this->localized($seo, 'seo_title', $locale) : '', 'description' => $seo ? $this->localized($seo, 'seo_description', $locale) : '', 'keywords' => $seo ? $this->localized($seo, 'seo_keywords', $locale) : '', 'canonical' => $this->safeUrl($seo?->canonical_url ?? ''), 'noindex' => (bool) $seo?->noindex, 'image' => $this->image($seo?->mainImage)],
            'about' => $about ? [...$this->card($about, $locale), 'content' => app(ProductHtml::class)->clean($this->localized($about, 'content', $locale))] : null,
            'products' => $products->map(fn (Product $product): array => [...$this->card($product, $locale), 'regular_price' => $product->regular_price, 'sale_price' => $product->sale_price, 'is_new' => $product->is_new]),
            'news' => $news->map(fn (Product $product): array => $this->card($product, $locale)),
        ]]);
    }

    private function localized(Product $product, string $field, string $locale): string
    {
        return $locale === 'en' ? (($product->translations['en'][$field] ?? '') ?: ($product->$field ?? '')) : ($product->$field ?? '');
    }

    private function image(?ProductMedia $image): ?array
    {
        return $image ? ['url' => $image->url, 'width' => $image->width, 'height' => $image->height] : null;
    }

    private function safeUrl(string $url): string
    {
        return filter_var($url, FILTER_VALIDATE_URL) && in_array(parse_url($url, PHP_URL_SCHEME), ['http', 'https'], true) ? $url : '';
    }

    private function card(Product $product, string $locale): array
    {
        return ['id' => $product->id, 'name' => $this->localized($product, 'name', $locale), 'description' => Str::limit(html_entity_decode(strip_tags($this->localized($product, 'description', $locale)), ENT_QUOTES, 'UTF-8'), 240), 'image' => $this->image($product->mainImage), 'image_alt' => $this->localized($product, 'image_alt', $locale)];
    }
}
