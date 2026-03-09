<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tasks')) return;

        Schema::create('tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->text('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('assigned_to')->nullable();
            $table->unsignedInteger('created_by');
            $table->enum('priority', ['urgent', 'high', 'normal', 'low'])->default('normal');
            $table->enum('status', ['pending', 'in_progress', 'done', 'blocked'])->default('pending');
            $table->date('due_date')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->enum('category', ['sales', 'development', 'support', 'hr', 'finance', 'operations', 'other'])->default('other');
            $table->unsignedInteger('project_id')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('assigned_to')->references('id')->on('staff');
            $table->foreign('created_by')->references('id')->on('staff');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
