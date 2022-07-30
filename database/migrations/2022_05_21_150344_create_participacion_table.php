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
        Schema::create('participacion', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('audiovisual_id');
            $table->unsignedBigInteger('persona_id');
            $table->string('personaje', 128)->nullable();

            $table->foreign('audiovisual_id')->references('id')->on('audiovisual');
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
        Schema::dropIfExists('participacion');
    }
};
