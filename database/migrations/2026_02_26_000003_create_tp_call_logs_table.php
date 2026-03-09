<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tp_call_logs')) return;

        Schema::create('tp_call_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('touchpoint_id')->constrained('tp_touchpoints')->onDelete('cascade');
            $table->unsignedInteger('called_by');
            $table->timestamp('call_time')->useCurrent();
            $table->string('outcome');
            $table->text('notes')->nullable();
            $table->date('follow_up_date')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tp_call_logs');
    }
};
