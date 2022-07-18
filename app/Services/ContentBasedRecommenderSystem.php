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

        // Géneros e idioma
        $generos = [];
        $idiomas = [];

        foreach ($audiovisuales as $audiovisual) {
            array_push($generos, $audiovisual->genero);
            array_push($idiomas, $audiovisual->idioma);
        }

        $this->addGenerosIdiomaScores($generos, $audiovisualScores);
        $this->addGenerosIdiomaScores($idiomas, $audiovisualScores);

        // TOTAL SCORES
        arsort($audiovisualScores);
        foreach ($audiovisuales as $audiovisual) {
            unset($audiovisualScores[$audiovisual->id]);
        }

        // Array de Audiovisual
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
}
