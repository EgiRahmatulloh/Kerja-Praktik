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
        Schema::table('letter_types', function (Blueprint $table) {
            $table->integer('last_number')->default(0);
            $table->string('kode_surat', 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('letter_types', function (Blueprint $table) {
            $table->dropColumn(['last_number', 'kode_surat']);
        });
    }
};
