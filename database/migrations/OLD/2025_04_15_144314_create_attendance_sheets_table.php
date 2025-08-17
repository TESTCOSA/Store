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

        Schema::create('ws_hr_attendance_sheets', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('department_id')->constrained()->onDelete('cascade');
        $table->date('month');
        $table->json('attendance_data');
        $table->integer('present_count')->default(0);
        $table->integer('absent_count')->default(0);
        $table->integer('annual_leave_count')->default(0);
        $table->integer('sick_leave_count')->default(0);
        $table->integer('emergency_leave_count')->default(0);
        $table->integer('public_holiday_count')->default(0);
        $table->integer('unpaid_leave_count')->default(0);
        $table->timestamps();

            $table->unique(['user_id', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ws_hr_attendance_sheets');
    }
};
