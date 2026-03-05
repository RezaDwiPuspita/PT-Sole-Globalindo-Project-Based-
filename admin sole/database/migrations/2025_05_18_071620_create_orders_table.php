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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->dateTime('order_date');
            $table->enum('payment_method', ['cash', 'transfer', 'credit_card']);
            $table->enum('status', ['in_cart', 'processing', 'received', 'in_progress', 'completed', 'cancelled']);
            $table->enum('type', ['online', 'offline']);
            $table->enum('payment_status', ['waiting_payment', 'paid'])->nullable();
            $table->dateTime('payment_time')->nullable();
            $table->decimal('total_amount', 12, 2);
            $table->string('payment_proof')->nullable();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
