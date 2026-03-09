<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('staff')) return;

        Schema::create('staff', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
            $table->enum('role', ['sales_rep', 'secretary', 'support', 'hr', 'finance', 'developer', 'tester', 'admin']);
            $table->text('pin');
            $table->text('telegram_id')->nullable();
            $table->integer('active')->default(1);
            $table->text('mobile')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
