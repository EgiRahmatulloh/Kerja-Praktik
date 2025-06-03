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
            $table->boolean('is_public')->default(true)->after('kode_surat');
            $table->text('deskripsi')->nullable()->after('nama_jenis');
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
            $table->dropColumn(['is_public', 'deskripsi']);
        });
    }
};
