<?php

namespace App\Services;

use App\Models\Audiovisual;

class SistemaRecomendacionBasadoContenido
{
    /**
     * Compara las puntuaciones de dos objetos.
     * 
     * @param Object $a Objecto cuya puntuación se quiere comparar.
     * @param Object $b Objecto cuya puntuación se quiere comparar.
     *
     * @return integer
     */
    function comparacionPuntuacion($a, $b) {
        if ($a->puntuacion == $b->puntuacion) {
            return 0;
        }
        return ($a->puntuacion > $b->puntuacion) ? -1 : 1;
    }

    /**
     * Calcula y devuelve 10 audiovisuales como recomendación en base al género, idioma, 
     * directores y actores de audiovisuales desde los que se parte.
     * 
     * @param Array $audiovisuales Array que contiene los audiovisuales a partir de los cuales
     * se calculan 10 audiovisuales como recomendación.
     *
     * @return Array
     */
    public function sugerenciasAudiovisuales($audiovisuales) {
        $puntuacionAudiovisuales = [];

        $generos = [];
        $idiomas = [];
        $participantes = [];

        foreach ($audiovisuales as $audiovisual) {
            array_push($generos, $audiovisual->genero);
            array_push($idiomas, $audiovisual->idioma);

            $participaciones = $audiovisual->participaciones;
            foreach ($participaciones as $participacion) {
                array_push($participantes, $participacion->persona);
            }
            $this->puntuacionParticipacion($participantes, $puntuacionAudiovisuales);
        }

        $this->puntuacionGeneroIdioma($generos, $puntuacionAudiovisuales);
        $this->puntuacionGeneroIdioma($idiomas, $puntuacionAudiovisuales);

        // Puntuación total
        arsort($puntuacionAudiovisuales);
        foreach ($audiovisuales as $audiovisual) {
            unset($puntuacionAudiovisuales[$audiovisual->id]);
        }

        // Array de Audiovisuales
        $audiovisualesOrdered = [];
        foreach ($puntuacionAudiovisuales as $key => $value) {
            $audiovisualesOrdered[$key] = Audiovisual::find($key);
        }

        $sugerenciasAudiovisuales = array_slice($audiovisualesOrdered, 0, 10, true);
        usort($sugerenciasAudiovisuales, array($this,"comparacionPuntuacion"));

        return $sugerenciasAudiovisuales;
    }

    /**
     * Añade al array de puntuaciones un audiovisual candidato a ser recomendado en base a que
     * tiene el mismo género o idioma que un audiovisual desde los que se parte y establece 1 como 
     * puntuación de ese audiovisual respecto a ser candidato.
     * En el caso de que ya exista el audiovisual en el array, suma 1 a la puntuación de ese audiovisual.
     * 
     * @param Array $array Array que contiene géneros o idiomas.
     * @param Array $puntuacionAudiovisuales Array que contiene los audiovisuales candidatos a ser
     * recomendados y su respectiva puntuación al respecto.
     *
     * @return void
     */
    private function puntuacionGeneroIdioma($array, &$puntuacionAudiovisuales) {
        foreach ($array as $elemento) {
            foreach ($elemento->audiovisuales as $audiovisual) {
                if (isset($puntuacionAudiovisuales[$audiovisual->id]))
                    $puntuacionAudiovisuales[$audiovisual->id] += 1;
                else
                    $puntuacionAudiovisuales[$audiovisual->id] = 1;
            }
        }
    }

    /**
     * Añade al array de puntuaciones un audiovisual candidato a ser recomendado en base a que
     * tiene el mismo actor o director que un audiovisual desde los que se parte y establece 0.5 o 1 
     * como puntuación de ese audiovisual dependiendo de si el participante se trata de un actor 
     * o de un director.
     * En el caso de que ya exista el audiovisual en el array, suma 0.5 o 1 a la puntuación de ese 
     * audiovisual dependiendo de si el participante se trata de un actor o de un director.
     * 
     * @param Array $participantes Array que contiene los participaciones de un audiovisual.
     * @param Array $puntuacionAudiovisuales Array que contiene los audiovisuales candidatos a ser
     * recomendados y su respectiva puntuación al respecto.
     *
     * @return void
     */
    private function puntuacionParticipacion($participantes, &$puntuacionAudiovisuales) {
        foreach ($participantes as $participante) {
            if ($participante->tipoParticipante_id === 1) {
                foreach ($participante->participaciones as $participacion) {
                    $audiovisual = $participacion->audiovisual;
                    if (isset($puntuacionAudiovisuales[$audiovisual->id]))
                        $puntuacionAudiovisuales[$audiovisual->id] += 0.5;
                    else
                        $puntuacionAudiovisuales[$audiovisual->id] = 0.5;
                }
            } else {
                foreach ($participante->participaciones as $participacion) {
                    $audiovisual = $participacion->audiovisual;
                    if (isset($puntuacionAudiovisuales[$audiovisual->id]))
                        $puntuacionAudiovisuales[$audiovisual->id] += 1;
                    else
                        $puntuacionAudiovisuales[$audiovisual->id] = 1;
                }
            }
        }
    }
}
