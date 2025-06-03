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
        Schema::create('letter_type_data_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('letter_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('data_item_id')->constrained()->onDelete('cascade');
            $table->unique(['letter_type_id', 'data_item_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('letter_type_data_item');
    }
};
