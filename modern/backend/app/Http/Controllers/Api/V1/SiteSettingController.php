<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SiteSettingController extends Controller
{
    public function branding(): JsonResponse
    {
        $data = SiteSetting::where('key', 'general')->first()?->data;

        return response()->json(['data' => ['vi' => $data['translations']['vi']['name'] ?? '', 'en' => $data['translations']['en']['name'] ?? '']]);
    }

    public function show(): JsonResponse
    {
        return response()->json(['data' => SiteSetting::where('key', 'general')->first()?->data ?? ['translations' => ['vi' => [], 'en' => []], 'options' => ['lang_default' => 'vi']]]);
    }

    public function update(Request $request): JsonResponse
    {
        $rules = [
            'translations' => ['required', 'array:vi,en'],
            'translations.vi' => ['required', 'array:name,address,slogan,copyright,keysearch'],
            'translations.en' => ['sometimes', 'array:name,address,slogan,copyright,keysearch'],
            'translations.vi.name' => ['required', 'string', 'max:255'],
            'options' => ['required', 'array:lang_default,email,hotline,phone,zalo,oaidzalo,website,fanpage,facebook,twitter,instagram,youtube,coords,link_googlemaps,worktime,coords_iframe'],
            'options.lang_default' => ['required', 'in:vi,en'],
        ];
        foreach (['name', 'address', 'slogan', 'copyright', 'keysearch'] as $field) {
            $rules['translations.*.'.$field] = ['nullable', 'string', 'max:1000'];
        }
        foreach (['hotline', 'phone', 'zalo', 'oaidzalo', 'coords', 'worktime'] as $field) {
            $rules['options.'.$field] = ['nullable', 'string', 'max:255'];
        }
        foreach (['website', 'fanpage', 'facebook', 'twitter', 'instagram', 'youtube', 'link_googlemaps'] as $field) {
            $rules['options.'.$field] = ['nullable', 'url:http,https', 'max:2048'];
        }
        $rules['options.email'] = ['nullable', 'email', 'max:255'];
        $rules['options.coords_iframe'] = ['nullable', 'string', 'max:10000'];
        foreach (['analytics', 'mastertool', 'headjs', 'bodyjs'] as $field) {
            $rules[$field] = ['nullable', 'string', 'max:30000'];
        }
        $data = $request->validate($rules);
        $setting = SiteSetting::updateOrCreate(['key' => 'general'], ['data' => $data]);

        return response()->json(['data' => $setting->data]);
    }
}
