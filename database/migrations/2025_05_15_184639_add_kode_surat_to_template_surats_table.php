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
        Schema::table('template_surats', function (Blueprint $table) {
            $table->string('kode_surat', 10)->nullable()->after('nama_template');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('template_surats', function (Blueprint $table) {
            $table->dropColumn('kode_surat');
        });
    }
};
