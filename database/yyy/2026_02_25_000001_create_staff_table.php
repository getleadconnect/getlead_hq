<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('mobile', 20)->unique();
            $table->string('pin');          // bcrypt 4-digit PIN
            $table->enum('role', [
                'admin', 'secretary', 'sales_rep', 'support',
                'hr', 'finance', 'developer', 'tester',
            ])->default('sales_rep');
            $table->boolean('active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        // Seed a default admin for first login
        \DB::table('staff')->insert([
            'name'       => 'Admin',
            'mobile'     => '9999999999',
            'pin'        => Hash::make('1234'),
            'role'       => 'admin',
            'active'     => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
