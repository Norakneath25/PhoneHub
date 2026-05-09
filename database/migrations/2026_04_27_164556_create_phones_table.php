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
        Schema::create('phones', function (Blueprint $table) {
            $table->id();
            $table->String('brand');
            $table->String('model');
            $table->decimal('price',10,2);
            $table->String('image');
            $table->String('store_url');
            $table->String('ram');
            $table->String('storage');
            $table->String('camera');
            $table->String('battery');
            $table->String('screen_size');
            $table->String('os');
            $table->date('release_date')->nullable();
            $table->decimal('rating', 3, 1)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phones');
    }
};
