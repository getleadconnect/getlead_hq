<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('clients', 'business_id')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->string('business_id')->nullable()->after('id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('clients', 'business_id')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->dropColumn('business_id');
            });
        }
    }
};
