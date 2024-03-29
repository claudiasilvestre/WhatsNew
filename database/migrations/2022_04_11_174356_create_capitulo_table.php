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
        Schema::create('capitulo', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('temporada_id');
            $table->integer('numero');
            $table->string('nombre', 2048)->nullable();
            $table->string('sinopsis', 2048)->nullable();
            $table->string('cartel', 64)->nullable();
            $table->date('fechaLanzamiento')->nullable();

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
        Schema::dropIfExists('capitulo');
    }
};
