<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CatalogTypes
{
    public static function all(): array
    {
        $result = [];
        foreach (['catalog' => 'products', 'news' => 'news', 'static' => 'news', 'seopage' => 'news'] as $config => $module) {
            foreach (config($config.'.types', []) as $key => $definition) {
                if (isset($result[$key])) {
                    throw new \LogicException('Duplicate catalog type: '.$key);
                }
                $defaults = array_replace_recursive(config('catalog.defaults'), config($config.'.defaults', []));
                $resolved = array_replace_recursive($defaults, $definition);
                $resolved['module'] = $module;
                $resolved['kind'] = $config;
                $resolved['singular'] = $definition['singular'] ?? $definition['label'];
                $resolved['singleton'] = in_array($config, ['static', 'seopage'], true);
                if ($resolved['singleton']) {
                    $resolved['features']['copy'] = false;
                    $resolved['category_depth'] = 0;
                    $resolved['features']['brand'] = false;
                    $resolved['features']['size'] = false;
                }
                $resolved['key'] = $key;
                $resolved['statuses'] = $definition['statuses'] ?? config($config.'.defaults.statuses', config('catalog.defaults.statuses'));
                $resolved['category_depth'] = max(0, min(4, (int) $resolved['category_depth']));
                $result[$key] = $resolved;
            }
        }

        return $result;
    }

    public static function resolve(Request $request, ?Model $record = null): array
    {
        $module = self::module($request);
        $types = array_filter(self::all(), fn (array $definition): bool => $definition['module'] === $module);
        $request->validate(['type' => ['sometimes', 'required', 'string', Rule::in(array_keys($types))]]);
        $key = $request->input('type', config($module === 'news' ? 'news.default' : 'catalog.default'));
        abort_unless(isset($types[$key]), 404);
        if ($record) {
            abort_unless($record->type === $key, 404);
        }

        return $types[$key];
    }

    public static function module(Request $request): string
    {
        return $request->is('api/v1/cms/news', 'api/v1/cms/news/*', 'api/v1/cms/news-*', 'api/v1/cms/static-types', 'api/v1/cms/seopage-types') ? 'news' : 'products';
    }

    public static function kinds(array $definition): array
    {
        return array_keys(array_filter([
            'category' => $definition['category_depth'] > 0,
            'brand' => $definition['features']['brand'],
            'size' => $definition['features']['size'],
        ]));
    }

    public static function productData(array $data, array $definition, bool $creating): array
    {
        $groups = [
            'code' => ['code'], 'pricing' => ['regular_price', 'sale_price', 'availability'],
            'rating' => ['rating'], 'brand' => ['brand_id'], 'size' => ['size_ids'],
            'description' => ['description'], 'content' => ['content'], 'specifications' => ['specifications'],
            'images' => ['main_image_id', 'image_alt', 'images'],
            'seo' => ['seo_title', 'seo_description', 'seo_keywords', 'canonical_url', 'noindex'],
            'schema' => ['schema_mode', 'schema_data'], 'ai_seo' => ['ai_seo'],
        ];
        foreach ($groups as $feature => $fields) {
            if ($definition['features'][$feature]) {
                continue;
            }
            foreach ($fields as $field) {
                if ($field === 'code' && $creating) {
                    continue;
                }
                unset($data[$field], $data['translations']['en'][$field]);
            }
        }
        if ($definition['category_depth'] === 0) {
            unset($data['category_id']);
        }
        foreach (['is_active', 'is_featured', 'is_new', 'is_bestseller'] as $field) {
            if (! isset($definition['statuses'][$field])) {
                unset($data[$field]);
            }
        }
        if (! $definition['features']['schema']) {
            $data['schema_mode'] = 'disabled';
            $data['schema_data'] = null;
        }

        return $data;
    }
}
