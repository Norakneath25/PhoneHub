<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('phone_store_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phone_id')->constrained()->cascadeOnDelete();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->decimal('price', 10, 2);
            $table->string('product_url')->nullable();
            $table->boolean('in_stock')->default(true);
            $table->timestamps();
            $table->unique(['phone_id', 'store_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phone_store_prices');
    }
};
