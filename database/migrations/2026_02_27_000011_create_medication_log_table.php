<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('medication_log')) return;

        Schema::create('medication_log', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date')->unique();
            $table->integer('morning')->default(0);
            $table->integer('night')->default(0);
            $table->integer('d_rise')->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medication_log');
    }
};
