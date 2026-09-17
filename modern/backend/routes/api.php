<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CmsUserController;
use App\Http\Controllers\Api\V1\HealthController;
use App\Http\Controllers\Api\V1\HomepageController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\PhotoController;
use App\Http\Controllers\Api\V1\ProductCategoryController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\ProductMediaController;
use App\Http\Controllers\Api\V1\RoleController;
use App\Http\Controllers\Api\V1\SiteSettingController;
use App\Http\Middleware\EnsureCmsSession;
use App\Http\Middleware\EnsurePasswordChanged;
use App\Http\Resources\CmsUserResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function (): void {
    Route::get('/health', HealthController::class)->name('health');
    Route::get('/website/home', HomepageController::class)->middleware('throttle:120,1');
    Route::get('/catalog/media/{media}', [ProductMediaController::class, 'show'])->name('catalog.media');
    Route::middleware('web')->group(function (): void {
        Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:login');
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::middleware(['auth:sanctum', EnsureCmsSession::class])->group(function (): void {
            Route::get('/me', fn (Request $request): UserResource => new UserResource($request->user()))->name('me');
            Route::get('/auth/me', fn (Request $request): CmsUserResource => new CmsUserResource($request->user()));
            Route::put('/auth/password', [AuthController::class, 'password'])->middleware('throttle:10,1');
            Route::middleware(EnsurePasswordChanged::class)->group(function (): void {
                Route::get('/cms/branding', [SiteSettingController::class, 'branding']);
                Route::get('/cms/order-config', [OrderController::class, 'configuration'])->middleware('can:orders.view');
                Route::get('/cms/orders', [OrderController::class, 'index'])->middleware('can:orders.view');
                Route::get('/cms/orders/{order}', [OrderController::class, 'show'])->middleware('can:orders.view');
                Route::post('/cms/orders', [OrderController::class, 'store'])->middleware('can:orders.manage');
                Route::put('/cms/orders/{order}', [OrderController::class, 'update'])->middleware('can:orders.manage');
                Route::get('/cms/photo-types', [PhotoController::class, 'types'])->middleware('can:media.view');
                Route::get('/cms/photos/{type}', [PhotoController::class, 'index'])->middleware('can:media.view');
                Route::post('/cms/photos/{type}', [PhotoController::class, 'save'])->middleware('can:media.manage');
                Route::put('/cms/photos/{type}/{photo}', [PhotoController::class, 'save'])->middleware('can:media.manage');
                Route::delete('/cms/photos/{type}/{photo}', [PhotoController::class, 'destroy'])->middleware('can:media.manage');
                Route::post('/cms/photo-media', [ProductMediaController::class, 'store'])->middleware(['can:media.manage', 'throttle:30,1']);
                Route::get('/cms/general-settings', [SiteSettingController::class, 'show'])->middleware('can:general.view');
                Route::put('/cms/general-settings', [SiteSettingController::class, 'update'])->middleware('can:general.manage');
                Route::middleware('can:products.view')->group(function (): void {
                    Route::get('/cms/product-types', [ProductController::class, 'types']);
                    Route::get('/cms/products', [ProductController::class, 'index']);
                    Route::get('/cms/products/{product}', [ProductController::class, 'show']);
                    Route::get('/cms/product-categories', [ProductCategoryController::class, 'index']);
                });
                Route::middleware('can:products.manage')->group(function (): void {
                    Route::post('/cms/products', [ProductController::class, 'store']);
                    Route::put('/cms/products/{product}', [ProductController::class, 'update']);
                    Route::patch('/cms/products/{product}/status', [ProductController::class, 'updateStatus']);
                    Route::delete('/cms/products/{product}', [ProductController::class, 'destroy']);
                    Route::post('/cms/products/{id}/restore', [ProductController::class, 'restore']);
                    Route::post('/cms/products/{product}/duplicate', [ProductController::class, 'duplicate']);
                    Route::post('/cms/product-categories', [ProductCategoryController::class, 'store']);
                    Route::put('/cms/product-categories/{category}', [ProductCategoryController::class, 'update']);
                    Route::delete('/cms/product-categories/{category}', [ProductCategoryController::class, 'destroy']);
                    Route::post('/cms/product-media', [ProductMediaController::class, 'store'])->middleware('throttle:30,1');
                });
                Route::middleware('can:content.view')->group(function (): void {
                    Route::get('/cms/news-types', [ProductController::class, 'types']);
                    Route::get('/cms/static-types', [ProductController::class, 'types']);
                    Route::get('/cms/seopage-types', [ProductController::class, 'types']);
                    Route::get('/cms/news', [ProductController::class, 'index']);
                    Route::get('/cms/news/{product}', [ProductController::class, 'show']);
                    Route::get('/cms/news-categories', [ProductCategoryController::class, 'index']);
                });
                Route::middleware('can:content.manage')->group(function (): void {
                    Route::post('/cms/news', [ProductController::class, 'store']);
                    Route::put('/cms/news/{product}', [ProductController::class, 'update']);
                    Route::patch('/cms/news/{product}/status', [ProductController::class, 'updateStatus']);
                    Route::delete('/cms/news/{product}', [ProductController::class, 'destroy']);
                    Route::post('/cms/news/{id}/restore', [ProductController::class, 'restore']);
                    Route::post('/cms/news/{product}/duplicate', [ProductController::class, 'duplicate']);
                    Route::post('/cms/news-categories', [ProductCategoryController::class, 'store']);
                    Route::put('/cms/news-categories/{category}', [ProductCategoryController::class, 'update']);
                    Route::delete('/cms/news-categories/{category}', [ProductCategoryController::class, 'destroy']);
                    Route::post('/cms/news-media', [ProductMediaController::class, 'store'])->middleware('throttle:30,1');
                });
                Route::get('/cms/access/{module}', function (string $module) {
                    $permission = $module === 'access' ? 'access.manage' : $module.'.view';
                    abort_unless($permission === 'access.manage' || array_key_exists($permission, config('cms.permissions')), 404);
                    Gate::authorize($permission);

                    return response()->json(['allowed' => true]);
                });
                Route::middleware('can:access.manage')->group(function (): void {
                    Route::get('/cms/users', [CmsUserController::class, 'index']);
                    Route::post('/cms/users', [CmsUserController::class, 'store']);
                    Route::put('/cms/users/{user}', [CmsUserController::class, 'update']);
                    Route::get('/cms/roles', [RoleController::class, 'index']);
                    Route::post('/cms/roles', [RoleController::class, 'store']);
                    Route::put('/cms/roles/{role}', [RoleController::class, 'update']);
                });
            });
        });
    });
});
