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
        Schema::create('entities', function (Blueprint $table) {
            $table->id();
            $table->string('kodeSurat');
            $table->string('noSurat')->unique();
            $table->string('name'); // Menggunakan 'name' sesuai model Entity
            $table->string('nik');
            $table->string('tempatTglLahir');
            $table->string('pekerjaan');
            $table->text('address')->nullable(); // Menggunakan 'address' sesuai model Entity
            $table->text('keterangan');
            $table->date('tglSurat');
            $table->string('ttd');
            $table->string('namaTtd');
            $table->string('type'); // 'usaha' or 'domisili'
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
        Schema::dropIfExists('entities');
    }
};
