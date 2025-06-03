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
        Schema::create('service_schedules', function (Blueprint $table) {
            $table->id();
            $table->time('start_time'); // Jam mulai pelayanan
            $table->time('end_time'); // Jam selesai pelayanan
            $table->boolean('is_active')->default(true); // Status aktif
            $table->integer('processing_time')->default(10)->comment('Lama proses dalam menit');
            $table->boolean('is_paused')->default(false)->comment('Status jeda jadwal pelayanan');
            $table->text('pause_message')->nullable()->comment('Pesan pengumuman saat jadwal dijeda');
            $table->time('pause_end_time')->nullable()->comment('Jam selesai jeda pelayanan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_schedules');
    }
};
