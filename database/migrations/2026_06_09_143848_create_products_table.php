<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('barcode', 50)->unique();
            $table->string('name', 100);
            $table->string('category', 50);
            $table->integer('price');
            $table->integer('purchase_price');
            $table->integer('stock')->default(0);
            $table->integer('min_stock')->default(5);
            $table->string('unit', 20)->default('pcs');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->timestamps();

            $table->index(['branch_id', 'category']);
            $table->index('barcode');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
