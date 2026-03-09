<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('chat_logs')) return;

        Schema::create('chat_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->text('session_id')->nullable();
            $table->unsignedInteger('staff_id')->nullable();
            $table->text('staff_name')->nullable();
            $table->text('role')->default('user');
            $table->text('message');
            $table->text('channel')->default('telegram');
            $table->text('message_id')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_logs');
    }
};
