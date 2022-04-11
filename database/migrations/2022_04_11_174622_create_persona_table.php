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
        Schema::create('persona', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tipoPersona_id');
            $table->unsignedBigInteger('tipoParticipante_id')->default(0);
            $table->string('nombre', 64);
            $table->string('apellidos', 128);
            $table->string('foto', 64);
            $table->string('usuario', 16)->nullable();
            $table->string('correo', 64)->nullable();
            $table->string('contrasenna', 32)->nullable();
            $table->string('biografia', 256)->nullable();

            $table->foreign('tipoPersona_id')->references('id')->on('tipo_persona');
            $table->foreign('tipoParticipante_id')->references('id')->on('tipo_participante');

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
        Schema::dropIfExists('persona');
    }
};
