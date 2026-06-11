<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 50)->unique();
            $table->foreignId('branch_id')->constrained('branches');
            $table->foreignId('cashier_id')->constrained('users');
            $table->datetime('transaction_date');
            $table->integer('subtotal');
            $table->integer('tax')->default(0);
            $table->integer('discount')->default(0);
            $table->integer('total');
            $table->integer('cash');
            $table->integer('change');
            $table->enum('status', ['completed', 'cancelled'])->default('completed');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['branch_id', 'transaction_date']);
            $table->index('invoice_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
