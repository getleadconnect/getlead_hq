<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('webhook_log')) return;

        Schema::create('webhook_log', function (Blueprint $table) {
            $table->increments('id');
            $table->text('event')->nullable();
            $table->text('payload')->nullable();
            $table->text('result')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_log');
    }
};
