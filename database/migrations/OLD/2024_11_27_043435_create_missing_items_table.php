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
        Schema::create('inv_missing_items', function (Blueprint $table) {
                $table->id(); // Primary key
                $table->unsignedBigInteger('item_id');
                $table->unsignedBigInteger('stock_id');
                $table->unsignedBigInteger('work_order_id');
                $table->unsignedBigInteger('reported_by');
                $table->unsignedBigInteger('warehouse_id');
                $table->integer('quantity');
                $table->tinyInteger('status')->default(0);
                $table->unsignedBigInteger('resolved_by')->nullable();
                $table->timestamp('reported_at')->useCurrent();
                $table->timestamp('resolved_at')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
                // Add foreign key constraints
                $table->foreign('item_id')->references('id')->on('inv_items');
                $table->foreign('stock_id')->references('id')->on('inv_stocks');
                $table->foreign('warehouse_id')->references('id')->on('inv_warehouses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inv_missing_items');
    }
};
