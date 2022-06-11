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
        Schema::create('visualizacion_temporada', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('temporada_id');
            $table->unsignedBigInteger('persona_id');

            $table->foreign('temporada_id')->references('id')->on('temporada');
            $table->foreign('persona_id')->references('id')->on('persona');

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
        Schema::dropIfExists('visualizacion_temporada');
    }
};
