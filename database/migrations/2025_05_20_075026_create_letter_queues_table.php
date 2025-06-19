<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('letter_queues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('filled_letter_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('service_schedule_id')->nullable();
            $table->dateTime('scheduled_date')->nullable(); // Jadwal antrian
            $table->integer('processing_time')->nullable(); // Lama proses dalam menit
            $table->enum('status', ['waiting', 'processing', 'completed'])->default('waiting');
            $table->text('notes')->nullable(); // Catatan tambahan
            $table->timestamps();
            
            $table->foreign('service_schedule_id')->references('id')->on('service_schedules')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('letter_queues');
    }
};