<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('login_history')) return;

        Schema::create('login_history', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('staff_id')->nullable();
            $table->text('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('staff_id')->references('id')->on('staff');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_history');
    }
};
