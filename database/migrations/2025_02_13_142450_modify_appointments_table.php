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
        Schema::table('appointments', function (Blueprint $table) {
            // $table->enum('status', ['upcoming', 'completed', 'canceled'])->default('upcoming')->change();
            $table->string('location')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {

            // $table->enum('status', ['upcoming', 'completed', 'canceled'])->default('pending')->change();
            $table->dropColumn('location');
        });
    }
};
