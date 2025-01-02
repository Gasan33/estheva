<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Promo code string (e.g., SAVE10)
            $table->decimal('discount_value', 8, 2); // Discount amount or percentage
            $table->enum('discount_type', ['percentage', 'fixed']); // Discount type (percentage or fixed)
            $table->date('expiration_date')->nullable(); // Optional expiration date
            $table->integer('usage_limit')->nullable(); // Limit on the number of uses
            $table->enum('status', ['active', 'expired', 'disabled'])->default('active'); // Promo code status
            $table->integer('usages')->default(0); // Track number of times used
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promo_codes');
    }
};
