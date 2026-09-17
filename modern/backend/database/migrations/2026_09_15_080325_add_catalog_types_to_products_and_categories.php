<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['products', 'product_categories'] as $name) {
            Schema::table($name, function (Blueprint $table) use ($name): void {
                $table->string('type', 100)->default('san-pham')->index();
                $table->dropUnique(['slug']);
                $table->unique(['type', 'slug']);
                if ($name === 'products') {
                    $table->dropUnique(['code']);
                    $table->unique(['type', 'code']);
                }
            });
        }
    }

    public function down(): void
    {
        foreach (['products', 'product_categories'] as $name) {
            Schema::table($name, function (Blueprint $table) use ($name): void {
                $table->dropUnique(['type', 'slug']);
                $table->unique('slug');
                if ($name === 'products') {
                    $table->dropUnique(['type', 'code']);
                    $table->unique('code');
                }
                $table->dropColumn('type');
            });
        }
    }
};
