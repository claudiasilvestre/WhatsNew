<?php

namespace App\Services;

use App\Models\Audiovisual;

class ContentBasedRecommenderSystem
{
    function comparacionPuntuacion($a, $b) {
        if ($a->puntuacion == $b->puntuacion) {
            return 0;
        }
        return ($a->puntuacion > $b->puntuacion) ? -1 : 1;
    }

    public function sugerenciasAudiovisuales($audiovisuales) {
        $audiovisualScores = [];

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
            $this->addParticipacionScores($participantes, $audiovisualScores);
        }

        $this->addGenerosIdiomaScores($generos, $audiovisualScores);
        $this->addGenerosIdiomaScores($idiomas, $audiovisualScores);

        // Puntuación total
        arsort($audiovisualScores);
        foreach ($audiovisuales as $audiovisual) {
            unset($audiovisualScores[$audiovisual->id]);
        }

        // Array de Audiovisuales
        $audiovisualesOrdered = [];
        foreach ($audiovisualScores as $key => $value) {
            $audiovisualesOrdered[$key] = Audiovisual::find($key);
        }

        $sugerenciasAudiovisuales = array_slice($audiovisualesOrdered, 0, 10, true);
        usort($sugerenciasAudiovisuales, array($this,"comparacionPuntuacion"));

        return $sugerenciasAudiovisuales;
    }

    private function addGenerosIdiomaScores($array, &$audiovisualScores) {
        foreach ($array as $elemento) {
            foreach ($elemento->audiovisuales as $audiovisual) {
                if (isset($audiovisualScores[$audiovisual->id]))
                    $audiovisualScores[$audiovisual->id] += 1;
                else
                    $audiovisualScores[$audiovisual->id] = 1;
            }
        }
    }

    private function addParticipacionScores($participantes, &$audiovisualScores) {
        foreach ($participantes as $participante) {
            if ($participante->tipoParticipante_id === 1) {
                foreach ($participante->participaciones as $participacion) {
                    $audiovisual = $participacion->audiovisual;
                    if (isset($audiovisualScores[$audiovisual->id]))
                        $audiovisualScores[$audiovisual->id] += 0.5;
                    else
                        $audiovisualScores[$audiovisual->id] = 0.5;
                }
            } else {
                foreach ($participante->participaciones as $participacion) {
                    $audiovisual = $participacion->audiovisual;
                    if (isset($audiovisualScores[$audiovisual->id]))
                        $audiovisualScores[$audiovisual->id] += 1;
                    else
                        $audiovisualScores[$audiovisual->id] = 1;
                }
            }
        }
    }
}
