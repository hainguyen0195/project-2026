<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('code', 100)->unique();
            $table->foreignId('category_id')->nullable()->constrained('product_categories')->restrictOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained('product_categories')->restrictOnDelete();
            $table->unsignedBigInteger('main_image_id')->nullable();
            $table->string('image_alt')->nullable();
            $table->decimal('regular_price', 15, 2)->default(0);
            $table->decimal('sale_price', 15, 2)->nullable();
            $table->string('availability', 20)->default('in_stock');
            $table->decimal('rating', 2, 1)->nullable();
            $table->longText('description')->nullable();
            $table->longText('content')->nullable();
            $table->longText('specifications')->nullable();
            $table->string('seo_title')->nullable();
            $table->string('seo_keywords', 500)->nullable();
            $table->text('seo_description')->nullable();
            $table->string('canonical_url', 2048)->nullable();
            $table->boolean('noindex')->default(false);
            $table->string('schema_mode', 20)->default('auto');
            $table->json('schema_data')->nullable();
            $table->json('translations')->nullable();
            $table->boolean('is_active')->default(false)->index();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_new')->default(false);
            $table->boolean('is_bestseller')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('product_size', function (Blueprint $table): void {
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_category_id')->constrained('product_categories')->restrictOnDelete();
            $table->primary(['product_id', 'product_category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_size');
        Schema::dropIfExists('products');
    }
};
