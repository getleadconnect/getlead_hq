<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('daily_reports')) return;

        Schema::create('daily_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('staff_id');
            $table->date('report_date');
            $table->text('report_data');
            $table->dateTime('submitted_at')->useCurrent();
            $table->timestamp('created_at')->useCurrent();
            $table->dateTime('updated_at')->nullable();

            $table->unique(['staff_id', 'report_date']);
            $table->foreign('staff_id')->references('id')->on('staff');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_reports');
    }
};
