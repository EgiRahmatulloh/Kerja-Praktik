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
        Schema::create('template_surats', function (Blueprint $table) {
            $table->id();
            $table->string('nama_template');
            
            // Menggunakan path file, bukan konten teks/biner
            $table->string('template_path'); 
            
            $table->boolean('aktif')->default(true);
            
            // Baris enum 'kategori_surat' sudah DIHAPUS dari sini.
            
            $table->integer('last_number')->default(0);
            $table->string('share_setting')->default('private'); // 'public', 'limited', 'private'
            $table->foreignId('owner_id')->nullable()->constrained('users')->onDelete('set null');
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
        Schema::dropIfExists('template_surats');
    }
};