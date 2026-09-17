<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ProductMedia;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProductMediaController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate(['image' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:8192', 'dimensions:max_width=5000,max_height=5000']]);
        $file = $request->file('image');
        $image = @imagecreatefromstring($file->get());
        abort_unless($image, 422, 'Không đọc được ảnh.');
        $width = imagesx($image);
        $height = imagesy($image);
        imagepalettetotruecolor($image);
        imagealphablending($image, true);
        imagesavealpha($image, true);
        ob_start();
        $encoded = imagewebp($image, null, 88);
        $bytes = ob_get_clean();
        abort_unless($encoded && $bytes !== '', 422, 'Không xử lý được ảnh.');
        $path = 'products/'.Str::uuid().'.webp';
        abort_unless(Storage::disk('local')->put($path, $bytes), 500, 'Không lưu được ảnh.');
        try {
            $media = ProductMedia::create(['path' => $path, 'original_name' => mb_substr($file->getClientOriginalName(), 0, 255), 'mime' => 'image/webp', 'width' => $width, 'height' => $height, 'bytes' => strlen($bytes), 'uploaded_by' => $request->user()->id]);
        } catch (\Throwable $error) {
            Storage::disk('local')->delete($path);
            throw $error;
        }

        return response()->json(['data' => $media], 201);
    }

    public function show(ProductMedia $media): BinaryFileResponse
    {
        abort_unless(Storage::disk('local')->exists($media->path), 404);

        return response()->file(Storage::disk('local')->path($media->path), ['Content-Type' => 'image/webp', 'X-Content-Type-Options' => 'nosniff', 'Cache-Control' => 'public, max-age=86400']);
    }
}
