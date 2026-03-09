<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('asset_repairs')) return;

        Schema::create('asset_repairs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('asset_id');
            $table->date('date');
            $table->text('issue');
            $table->double('cost')->default(0);
            $table->text('vendor')->nullable();
            $table->text('status')->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('asset_id')->references('id')->on('assets');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_repairs');
    }
};
