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
        Schema::create('ws_hr_attendance_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_sheet_id');
            $table->string('period_label');
            $table->string('department');

            // Manager approval
            $table->boolean('manager_approval')->default(false);
            $table->timestamp('manager_approved_at')->nullable();
            $table->text('manager_comments')->nullable();

            // HR approval
            $table->boolean('hr_approval')->default(false);
            $table->timestamp('hr_approved_at')->nullable();
            $table->text('hr_comments')->nullable();

            // Accountant approval
            $table->boolean('accountant_approval')->default(false);
            $table->timestamp('accountant_approved_at')->nullable();
            $table->text('accountant_comments')->nullable();

            // Director approval
            $table->boolean('director_approval')->default(false);
            $table->timestamp('director_approved_at')->nullable();
            $table->text('director_comments')->nullable();

            // Overall status
            $table->string('status')->default('pending');

            $table->foreign('attendance_sheet_id')->references('id')->on('ws_hr_attendance_sheets');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ws_hr_attendance_approvals');
    }
};
