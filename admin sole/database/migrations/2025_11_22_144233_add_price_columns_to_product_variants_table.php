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
        Schema::table('product_variants', function (Blueprint $table) {
            // Tambahkan kolom untuk harga material per 10cm
            $table->decimal('price_per_10cm', 10, 2)->nullable()->after('name');
            
            // Harga per dimensi (opsional, lebih fleksibel)
            $table->decimal('length_price', 10, 2)->nullable()->after('price_per_10cm');
            $table->decimal('width_price', 10, 2)->nullable()->after('length_price');
            $table->decimal('height_price', 10, 2)->nullable()->after('width_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn(['price_per_10cm', 'length_price', 'width_price', 'height_price']);
        });
    }
};
