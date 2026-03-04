<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tp_customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('company')->nullable();
            $table->string('phone');
            $table->string('email')->nullable();
            $table->enum('subscription_type', ['free_trial', 'extended_trial', '1_month', '3_month', '1_year']);
            $table->date('start_date');
            $table->date('expiry_date');
            $table->enum('status', ['active', 'renewed', 'churned'])->default('active');
            $table->enum('health', ['healthy', 'at_risk', 'critical', 'churning', 'unknown'])->default('unknown');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tp_customers');
    }
};
