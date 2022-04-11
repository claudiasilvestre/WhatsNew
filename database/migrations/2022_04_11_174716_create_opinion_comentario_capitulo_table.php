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
        Schema::create('opinion_comentario_capitulo', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('persona_id');
            $table->unsignedBigInteger('comentarioCapitulo_id');
            $table->integer('opinion');

            $table->foreign('persona_id')->references('id')->on('persona');
            $table->foreign('comentarioCapitulo_id')->references('id')->on('comentario_capitulo');

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
        Schema::dropIfExists('opinion_comentario_capitulo');
    }
};
