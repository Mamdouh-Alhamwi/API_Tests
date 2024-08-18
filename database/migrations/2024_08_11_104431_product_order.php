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
        Schema::create('product_order', function(Blueprint $table){

            $table->id();
          
            $table->unsignedBigInteger('products_id');
            $table->foreign('products_id')->references('id')->on('products');
          
            $table->unsignedBigInteger('orders_id');
            $table->foreign('orders_id')->references('id')->on('orders');

            /*$table->foreignId('order_id')->constrained('orders');
            $table->foreignId('product_id')->constrained('products');*/

            $table->integer('product_quantity');

            $table->boolean('liked')->default(false); 
            //$table->integer('quantity');
            $table->timestamps();
          
          });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //Schema::dropIfExists('product_order');
    }
};
