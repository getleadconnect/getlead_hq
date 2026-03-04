<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tasks')) {
            return;
        }
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('staff')->nullOnDelete();
            $table->foreignId('created_by')->constrained('staff')->cascadeOnDelete();
            $table->enum('status', ['pending', 'in_progress', 'blocked', 'done'])->default('pending');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->enum('category', ['sales', 'development', 'support', 'hr', 'finance', 'operations', 'other'])->default('other');
            $table->unsignedBigInteger('project_id')->nullable();
            $table->date('due_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
