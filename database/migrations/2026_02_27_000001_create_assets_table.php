<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('assets')) return;

        Schema::create('assets', function (Blueprint $table) {
            $table->increments('id');
            $table->text('asset_tag')->nullable();
            $table->text('name');
            $table->text('type');
            $table->text('brand')->nullable();
            $table->text('model')->nullable();
            $table->text('serial_number')->nullable();
            $table->date('purchase_date')->nullable();
            $table->double('purchase_price')->nullable();
            $table->text('vendor')->nullable();
            $table->unsignedInteger('assigned_to')->nullable();
            $table->text('status')->default('active');
            $table->date('warranty_expiry')->nullable();
            $table->text('notes')->nullable();
            $table->text('remarks')->nullable();
            $table->integer('checkup_interval')->default(90);
            $table->date('last_checkup')->nullable();
            $table->date('next_checkup')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('assigned_to')->references('id')->on('staff');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
