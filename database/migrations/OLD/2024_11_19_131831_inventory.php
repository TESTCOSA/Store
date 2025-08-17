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

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
        Schema::create('inv_supplier', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('status');
            $table->string('address');
            $table->timestamps();
        });
        Schema::create('inv_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_calibrated')->default(false);
            $table->boolean('is_maintained')->default(false);
            $table->boolean('is_returned')->default(false);
            $table->boolean('is_consumable')->default(false);
            $table->timestamps();
        });
        Schema::create('inv_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('types_id')->constrained('inv_types'); // This is where the foreign key references 'types'
            $table->boolean('enabled')->default(true);
            $table->integer('sort')->default(0);
            $table->timestamps();
        });
        Schema::create('inv_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('inv_categories');
            $table->string('name');
            $table->string('size')->nullable();
            $table->string('serial_number')->unique();
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->string('test_tag')->nullable();
            $table->integer('low_stock')->default(1);
            $table->timestamps(); // optional: for created_at and updated_at timestamps
        });
        Schema::create('inv_calibrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('items_id')->constrained('inv_items');
            $table->string('number')->nullable();
            $table->date('date')->nullable();
            $table->date('due_date')->nullable();
            $table->string('file')->nullable();
            $table->timestamps();
        });
        Schema::create('inv_warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('region');
            $table->string('name');
            $table->text('address');
            $table->timestamps();
        });
        Schema::create('inv_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('inv_items');
            $table->integer('quantity');
            $table->integer('available_quantity');
            $table->foreignId('warehouse_id')->constrained('inv_warehouses');
            $table->timestamps();
        });
        Schema::create('inv_stock_in', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained('inv_warehouses');
            $table->string('stocked_by');
            $table->date('stocked_date');
            $table->string('approved_by')->nullable();
            $table->boolean('approved')->default(false);
            $table->date('approve_date')->nullable();
            $table->string('status');
            $table->timestamps();
        });
        Schema::create('inv_stock_out', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wo_id');
            $table->foreignId('warehouse_id')->constrained('inv_warehouses');
            $table->string('request_by');
            $table->date('request_date');
            $table->string('approved_by')->nullable();
            $table->boolean('approved')->default(false);
            $table->date('approve_date')->nullable();
            $table->string('status');
            $table->timestamps();
        });
        Schema::create('inv_maintenance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('inv_suppliers');
            $table->string('maintenance_by');
            $table->date('maintenance_stock_out_date');
            $table->string('approved_by')->nullable();
            $table->date('approve_date')->nullable();
            $table->string('status');
            $table->timestamps();
        });
        Schema::create('inv_calibration_out', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('inv_suppliers');
            $table->string('calibration_by');
            $table->date('calibration_stock_out_date');
            $table->string('approved_by')->nullable();
            $table->date('approve_date')->nullable();
            $table->string('status');
            $table->timestamps();
        });

        Schema::create('inv_stock_in_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_in_id')->constrained('inv_stock_in');
            $table->foreignId('supplier_id')->constrained('inv_suppliers');
            $table->foreignId('stock_id')->constrained('inv_stocks');
            $table->foreignId('item_id')->constrained('inv_items');
            $table->integer('quantity');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
        Schema::create('inv_stock_out_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_out_id')->constrained('inv_stock_out');
            $table->foreignId('stock_id')->constrained('inv_stocks');
            $table->foreignId('item_id')->constrained('inv_items');
            $table->integer('quantity');
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('inv_calibration_out_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calibration_out_id')->constrained('inv_calibration_out');
            $table->foreignId('item_id')->constrained('inv_items');
            $table->string('calibration_number');
            $table->date('calibration_date');
            $table->date('calibration_due_date');
            $table->timestamps();
        });
        Schema::create('inv_maintenance_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_id')->constrained('inv_maintenance');
            $table->foreignId('stock_id')->constrained('inv_stocks');
            $table->foreignId('item_id')->constrained('inv_items');
            $table->string('maintenance_number');
            $table->date('maintenance_date');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('inv_supplier');
        Schema::dropIfExists('inv_types');
        Schema::dropIfExists('inv_categories');
        Schema::dropIfExists('inv_calibrations');
        Schema::dropIfExists('inv_items');
        Schema::dropIfExists('inv_warehouses');
        Schema::dropIfExists('inv_stocks');
        Schema::dropIfExists('inv_stock_in');
        Schema::dropIfExists('inv_stock_out');
        Schema::dropIfExists('inv_maintenance');
        Schema::dropIfExists('inv_calibration_out');
        Schema::dropIfExists('inv_stock_in_details');
        Schema::dropIfExists('inv_stock_out_details');
        Schema::dropIfExists('inv_maintenance_details');
    }
};
