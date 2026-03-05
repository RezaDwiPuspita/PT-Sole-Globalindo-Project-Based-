<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('custom_products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('material');
            $table->decimal('material_price', 12, 2);
            $table->string('wood_color')->nullable();
            $table->decimal('wood_color_price', 12, 2)->default(0);
            $table->string('rattan_color')->nullable();
            $table->decimal('rattan_color_price', 12, 2)->default(0);
            $table->decimal('length', 8, 2);
            $table->decimal('width', 8, 2);
            $table->decimal('height', 8, 2);
            $table->decimal('total_price', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_products');
    }
};
