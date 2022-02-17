<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Poligono;

use Solidbase\Geometria\Dominio\Fabrica\PolilinhaFabrica;
use Solidbase\Geometria\Dominio\Polilinha;

class AplicarFilletPoligono
{
    public static function executar(float $raio, int $indiceVertice, Polilinha $polilinha): Polilinha
    {
        if (!entre(0, $indiceVertice, count($polilinha) - 1)) {
            return $polilinha;
        }
        $pontos = $polilinha->pontos();
        $pontosNovos = [];
        for ($i = 0; $i < count($polilinha); ++$i) {
            if ($i == $indiceVertice) {
                $p1 = $pontos[$i - 1];
                $p2 = $pontos[$i];
                $p3 = $pontos[$i + 1];
                $pontosConcordancia = ConcordanciaPoligono::executar($p1, $p2, $p3, $raio);
                $pontosNovos[] = $pontosConcordancia[0];
                $pontosNovos[] = $pontosConcordancia[1];

                continue;
            }
            $pontosNovos[] = $pontos[$i];
        }

        return PolilinhaFabrica::criarPolilinhaPontos($pontosNovos, fechado: $polilinha->ePoligono());
    }
}
