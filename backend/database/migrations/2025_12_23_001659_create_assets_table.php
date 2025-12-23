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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('symbol', 10); // e.g., BTC, ETH
            $table->decimal('amount', 15, 8)->default(0.00000000); // Available asset
            $table->decimal('locked_amount', 15, 8)->default(0.00000000); // Reserved for open sell orders
            $table->timestamps();

            $table->unique(['user_id', 'symbol']); // A user can only have one entry per asset symbol
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
