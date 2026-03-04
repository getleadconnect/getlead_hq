<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('task_history')) {
            return;
        }
        Schema::create('task_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->string('action');
            $table->string('old_value')->nullable();
            $table->string('new_value')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_history');
    }
};
