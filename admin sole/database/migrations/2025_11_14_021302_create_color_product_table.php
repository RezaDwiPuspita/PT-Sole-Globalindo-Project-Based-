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
        Schema::create('color_product', function (Blueprint $table) {
            $table->id();

            // Relasi ke produk
            $table->foreignId('product_id')
                  ->constrained()
                  ->onDelete('cascade');

            // Relasi ke master warna
            $table->foreignId('color_id')
                  ->constrained()
                  ->onDelete('cascade');

            // Tambahan harga untuk warna ini di produk ini
            $table->integer('extra_price')->default(0);

            // Menandai apakah warna ini default untuk produk tsb
            $table->boolean('is_default')->default(false);

            $table->timestamps();

            // (Opsional) kombinasi unik produk-warna
            // $table->unique(['product_id', 'color_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('color_product');
    }
};
