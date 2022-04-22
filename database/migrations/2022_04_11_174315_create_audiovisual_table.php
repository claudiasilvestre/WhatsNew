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
            $table->string('titulo', 64)->nullable();
            $table->string('tituloOriginal', 64)->nullable();
            $table->integer('anno')->nullable();
            $table->string('pais', 32)->nullable();
            $table->integer('duracion')->nullable();
            $table->string('sinopsis', 1024)->nullable();
            $table->string('cartel', 64)->nullable();
            $table->decimal('puntuacion', $precision = 8, $scale = 1)->default(0.0);
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
