<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('hr_daily_reports')) return;

        Schema::create('hr_daily_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->date('report_date')->unique();
            $table->integer('total_employees')->default(0);
            $table->integer('present_count')->default(0);
            $table->integer('half_day_count')->default(0);
            $table->integer('full_day_leave_count')->default(0);
            $table->integer('interviews_scheduled')->default(0);
            $table->integer('interviews_completed')->default(0);
            $table->text('hr_note')->nullable();
            $table->unsignedInteger('submitted_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->dateTime('updated_at')->nullable();

            $table->foreign('submitted_by')->references('id')->on('staff');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_daily_reports');
    }
};
