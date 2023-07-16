<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->string('slug')->unique();
            $table->unsignedInteger('price');
            $table->unsignedInteger('discount_price')->default(0);
            $table->string('currency')->default('USD');
            $table->unsignedInteger('qty')->default(0);

            $table->unique(['size_id','product_id']);

            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('size_id')->constrained('variant_sizes')->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_variants');
    }
};
