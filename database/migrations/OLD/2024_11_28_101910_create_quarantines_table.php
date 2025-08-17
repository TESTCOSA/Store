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
        Schema::create('inv_quarantines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id'); // Reference to the item
            $table->integer('user_id'); // Reference to the user who initiated the action
            $table->text('reason')->nullable(); // Reason for quarantine
            $table->string('status')->default('quarantined'); // Current status (e.g., quarantined, released)
            $table->timestamp('quarantined_at')->nullable(); // When the item was quarantined
            $table->timestamp('released_at')->nullable(); // When the item was released from quarantine
            $table->timestamps(); // Created at, updated at


            $table->foreign('item_id')->references('id')->on('inv_items');
            $table->foreign('user_id')->references('user_id')->on('ws_users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quarantines');
    }
};
