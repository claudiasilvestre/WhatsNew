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
        Schema::create('actividad', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('persona_id');
            $table->integer('tipo');
            $table->unsignedBigInteger('audiovisual_id')->nullable();
            $table->unsignedBigInteger('capitulo_id')->nullable();
            $table->unsignedBigInteger('temporada_id')->nullable();

            $table->foreign('persona_id')->references('id')->on('persona');
            $table->foreign('audiovisual_id')->references('id')->on('audiovisual');
            $table->foreign('capitulo_id')->references('id')->on('capitulo');
            $table->foreign('temporada_id')->references('id')->on('temporada');

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
        Schema::dropIfExists('actividad');
    }
};
