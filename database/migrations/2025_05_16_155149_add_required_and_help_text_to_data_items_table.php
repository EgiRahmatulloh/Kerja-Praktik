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
        Schema::table('data_items', function (Blueprint $table) {
            $table->boolean('required')->default(false);
            $table->string('help_text')->nullable();
        });
    }

    public function down()
    {
        Schema::table('data_items', function (Blueprint $table) {
            $table->dropColumn(['required', 'help_text']);
        });
    }
};
