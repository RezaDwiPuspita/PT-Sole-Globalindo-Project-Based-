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
        Schema::table('products', function (Blueprint $table) {
            $table->string('default_length')->nullable();
            $table->string('default_width')->nullable();
            $table->string('default_height')->nullable();
            $table->string('default_bahan')->nullable();
            $table->string('default_color')->nullable();
            $table->string('default_rotan_color')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
