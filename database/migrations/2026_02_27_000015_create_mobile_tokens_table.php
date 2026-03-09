<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('mobile_tokens')) return;

        Schema::create('mobile_tokens', function (Blueprint $table) {
            $table->string('token', 500)->primary();
            $table->unsignedInteger('staff_id');
            $table->text('device_info')->nullable();
            $table->dateTime('expires_at');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('staff_id')->references('id')->on('staff');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mobile_tokens');
    }
};
