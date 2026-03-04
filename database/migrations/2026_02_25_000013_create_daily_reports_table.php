<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('daily_reports')) {
            return;
        }

        Schema::create('daily_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->date('report_date');
            $table->json('report_data')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');
            $table->unique(['staff_id', 'report_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_reports');
    }
};
