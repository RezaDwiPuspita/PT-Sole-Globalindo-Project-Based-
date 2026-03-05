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
        Schema::table('shipping_costs', function (Blueprint $table) {
            $table->decimal('total_volume_cm3', 14, 2)->default(0);
            $table->integer('total_items')->default(0);
            $table->json('item_summary')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipping_costs', function (Blueprint $table) {
            $table->dropColumn(['total_volume_cm3', 'total_items', 'item_summary']);
        });
    }
};
