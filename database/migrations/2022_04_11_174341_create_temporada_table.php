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
        Schema::create('temporada', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('audiovisual_id');
            $table->integer('numero');
            $table->string('nombre', 128);
            $table->integer('numeroCapitulos')->nullable();

            $table->foreign('audiovisual_id')->references('id')->on('audiovisual');

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
        Schema::dropIfExists('temporada');
    }
};
