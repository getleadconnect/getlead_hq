<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Links an HR employee to a staff (login) account.
 *
 * The Staff Section resolves the signed-in staff to their employee record via
 * hr_employees.staff_id. This column was added manually in dev; this migration
 * makes it reproducible on other environments (guarded so it's a no-op if present).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('hr_employees', 'staff_id')) {
            Schema::table('hr_employees', function (Blueprint $table) {
                $table->unsignedBigInteger('staff_id')->nullable()->index()->after('id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('hr_employees', 'staff_id')) {
            Schema::table('hr_employees', function (Blueprint $table) {
                $table->dropColumn('staff_id');
            });
        }
    }
};
