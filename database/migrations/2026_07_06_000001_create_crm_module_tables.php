<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * CRM module (ported from the standalone "crmdemo" portal).
 *
 * These tables power the demo-video CRM: customers get a personalised watch
 * link, every visit is recorded, and watch/engagement data drives analytics.
 *
 * NOTE: Per project decision for this module, there are NO Eloquent models —
 * all access goes through the query builder (DB facade) inside dedicated
 * App\Http\Controllers\Crm controllers. `created_by` references staff.id
 * (the existing auth table) instead of the original crmdemo `users` table.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Customers — one personalised demo link per customer.
        Schema::create('crm_customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('mobile', 50);
            $table->string('token')->unique();
            $table->unsignedBigInteger('created_by'); // staff.id
            $table->text('notes')->nullable();
            $table->integer('views_count')->default(0);
            $table->dateTime('last_viewed_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('token');
            $table->index('created_by');
        });

        // Customer views — one row per visit to a customer's watch link.
        Schema::create('crm_customer_views', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->integer('view_number');
            $table->string('ip_address', 64)->nullable();
            $table->string('browser', 100)->nullable();
            $table->string('os', 100)->nullable();
            $table->string('device', 100)->nullable();
            $table->string('city', 120)->nullable();
            $table->string('region', 120)->nullable();
            $table->string('country', 120)->nullable();
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->text('referrer')->nullable();
            $table->dateTime('viewed_at')->useCurrent();

            $table->index('customer_id');
            $table->foreign('customer_id')->references('id')->on('crm_customers')->cascadeOnDelete();
        });

        // Watch sessions — a single play-through of the video within a view.
        Schema::create('crm_watch_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('view_id');
            $table->double('video_duration')->nullable();
            $table->double('watch_duration')->default(0);
            $table->double('watch_percentage')->default(0);
            $table->boolean('completed')->default(false);
            $table->boolean('skipped')->default(false);
            $table->timestamp('created_at')->useCurrent();
            $table->dateTime('ended_at')->nullable();

            $table->index('view_id');
            $table->foreign('view_id')->references('id')->on('crm_customer_views')->cascadeOnDelete();
        });

        // Engagement events — play/pause/seek/complete markers within a session.
        Schema::create('crm_engagement_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('session_id');
            $table->string('event_type', 50);
            $table->double('event_time')->nullable();
            $table->text('payload')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('session_id');
            $table->foreign('session_id')->references('id')->on('crm_watch_sessions')->cascadeOnDelete();
        });

        // Watch segments — start/end ranges actually watched (retention heatmap).
        Schema::create('crm_watch_segments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('session_id');
            $table->double('start_time');
            $table->double('end_time');
            $table->timestamp('created_at')->useCurrent();

            $table->index('session_id');
            $table->foreign('session_id')->references('id')->on('crm_watch_sessions')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_watch_segments');
        Schema::dropIfExists('crm_engagement_events');
        Schema::dropIfExists('crm_watch_sessions');
        Schema::dropIfExists('crm_customer_views');
        Schema::dropIfExists('crm_customers');
    }
};
