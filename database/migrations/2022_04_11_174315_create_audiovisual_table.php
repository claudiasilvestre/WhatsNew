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
            $table->unsignedBigInteger('id')->primary();
            $table->unsignedBigInteger('tipoAudiovisual_id');
            $table->unsignedBigInteger('genero_id')->nullable();
            $table->unsignedBigInteger('idioma_id')->nullable();
            $table->string('titulo', 2048);
            $table->string('tituloOriginal', 64)->nullable();
            $table->integer('anno')->nullable();
            $table->integer('duracion')->nullable();
            $table->string('sinopsis', 2048)->nullable();
            $table->string('cartel', 64)->nullable();
            $table->date('fechaLanzamiento')->nullable();
            $table->decimal('puntuacion', $precision = 8, $scale = 2)->nullable();
            $table->integer('estado')->default(0);
            $table->integer('numeroTemporadas')->nullable();
            $table->integer('numeroCapitulos')->nullable();

            $table->foreign('tipoAudiovisual_id')->references('id')->on('tipo_audiovisual');
            $table->foreign('genero_id')->references('id')->on('genero');
            $table->foreign('idioma_id')->references('id')->on('idioma');

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
