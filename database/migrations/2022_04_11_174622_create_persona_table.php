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
            $table->unsignedBigInteger('tipoPersona_id')->default(1);
            $table->unsignedBigInteger('tipoParticipante_id')->nullable();
            $table->string('nombre', 128);
            $table->string('foto', 64)->nullable();
            $table->string('personaje', 128)->nullable();

            $table->string('usuario', 16)->nullable()->unique();
            $table->string('email')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->integer('seguidos')->default(0);
            $table->integer('seguidores')->default(0);
            $table->integer('puntos')->default(0);

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
