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
        Schema::create('audiovisual', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tipoAudiovisual_id');
            $table->unsignedBigInteger('genero_id');
            $table->string('titulo', 64);
            $table->string('tituloOriginal', 64);
            $table->integer('anno');
            $table->string('pais', 32);
            $table->integer('duracion');
            $table->string('sinopsis', 256);
            $table->string('cartel', 64);
            $table->integer('puntuacion')->default(0);
            $table->integer('estado')->default(0);

            $table->foreign('tipoAudiovisual_id')->references('id')->on('tipo_audiovisual');
            $table->foreign('genero_id')->references('id')->on('genero');

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
        Schema::dropIfExists('audiovisual');
    }
};
