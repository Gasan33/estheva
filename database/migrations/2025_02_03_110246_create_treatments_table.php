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
        Schema::create('treatments', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->json('images')->nullable();
            $table->boolean('home_based')->default(false);
            $table->string('video')->nullable();
            $table->integer('duration')->default(0);
            $table->json('benefits')->nullable();
            $table->json('instructions')->nullable();
            $table->decimal('discount_value', 8, 2)->nullable();
            $table->enum('discount_type', ['percentage', 'fixed'])->default('percentage');
            $table->double('treatment_sale_tag')->nullable();
            $table->unsignedBigInteger('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treatments');
    }
};
