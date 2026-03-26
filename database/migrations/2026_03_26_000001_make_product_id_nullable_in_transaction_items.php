<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaction_items', function (Blueprint $table) {
            // Drop the existing restrictOnDelete foreign key
            $table->dropForeign(['product_id']);

            // Make product_id nullable
            $table->uuid('product_id')->nullable()->change();

            // Re-add foreign key with nullOnDelete so products can be deleted
            // Transaction history is preserved via product_name, product_price, etc.
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('transaction_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);

            $table->uuid('product_id')->nullable(false)->change();

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->restrictOnDelete();
        });
    }
};
