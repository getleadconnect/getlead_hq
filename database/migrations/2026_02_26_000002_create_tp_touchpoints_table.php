<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tp_touchpoints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('tp_customers')->cascadeOnDelete();
            $table->string('stage');
            $table->date('due_date');
            $table->integer('assigned_to')->nullable();
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->string('outcome')->nullable();
            $table->text('outcome_notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tp_touchpoints');
    }
};
