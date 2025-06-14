<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('letter_queues', function (Blueprint $table) {
            $table->unsignedBigInteger('service_schedule_id')->nullable()->after('filled_letter_id');
            $table->foreign('service_schedule_id')->references('id')->on('service_schedules')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('letter_queues', function (Blueprint $table) {
            $table->dropForeign(['service_schedule_id']);
            $table->dropColumn('service_schedule_id');
        });
    }
};