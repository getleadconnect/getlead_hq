<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('qr_codes')) return;

        Schema::create('qr_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('qr_code', 500)->unique();
            $table->unsignedInteger('asset_id')->nullable();
            $table->dateTime('mapped_at')->nullable();
            $table->unsignedInteger('mapped_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('asset_id')->references('id')->on('assets');
            $table->foreign('mapped_by')->references('id')->on('staff');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_codes');
    }
};
