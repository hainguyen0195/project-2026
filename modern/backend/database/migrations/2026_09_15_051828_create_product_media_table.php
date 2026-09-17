<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_media', function (Blueprint $table): void {
            $table->id();
            $table->string('path')->unique();
            $table->string('original_name');
            $table->string('mime', 50);
            $table->unsignedInteger('width');
            $table->unsignedInteger('height');
            $table->unsignedBigInteger('bytes');
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
        Schema::table('products', fn (Blueprint $table) => $table->foreign('main_image_id')->references('id')->on('product_media')->restrictOnDelete());
        Schema::table('product_categories', fn (Blueprint $table) => $table->foreign('image_id')->references('id')->on('product_media')->restrictOnDelete());
        Schema::create('product_images', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('media_id')->constrained('product_media')->restrictOnDelete();
            $table->string('alt')->nullable();
            $table->string('caption')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->unique(['product_id', 'media_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_images');
        Schema::table('products', fn (Blueprint $table) => $table->dropForeign(['main_image_id']));
        Schema::table('product_categories', fn (Blueprint $table) => $table->dropForeign(['image_id']));
        Schema::dropIfExists('product_media');
    }
};
