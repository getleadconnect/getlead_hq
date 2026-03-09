<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('projects')) return;

        Schema::create('projects', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'on_hold', 'completed', 'archived'])->default('active');
            $table->unsignedInteger('project_lead')->nullable();
            $table->date('start_date')->nullable();
            $table->date('target_date')->nullable();
            $table->unsignedInteger('created_by');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('project_lead')->references('id')->on('staff')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('created_by')->references('id')->on('staff')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
