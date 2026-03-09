<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('asset_assignments')) return;

        Schema::create('asset_assignments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('asset_id');
            $table->unsignedInteger('staff_id')->nullable();
            $table->date('assigned_at');
            $table->date('returned_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('asset_id')->references('id')->on('assets');
            $table->foreign('staff_id')->references('id')->on('staff');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_assignments');
    }
};
