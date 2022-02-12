<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Poligono;

use Solidbase\Geometria\Dominio\Polilinha;
use Solidbase\Geometria\Dominio\Ponto;

class CentroPoligono
{
    private function __construct()
    {
    }

    public static function executar(Polilinha $poligono): ?Ponto
    {
        $area = AreaPoligono::executar($poligono);
        if (null === $area) {
            return null;
        }
        $somaX = 0;
        $somaY = 0;
        $pontos = $poligono->pontos();
        $numPontos = \count($poligono);

        for ($i = 0; $i < $numPontos - 1; ++$i) {
            $ponto = $pontos[$i];
            $proximo = $pontos[$i + 1];
            $comum = (($ponto->x * $proximo->y) - ($ponto->y * $proximo->x));
            $somaX += (($proximo->x + $ponto->x) * ($comum));
            $somaY += (($proximo->y + $ponto->y) * ($comum));
        }

        $x = $somaX / (6 * $area);
        $y = $somaY / ($area * 6);

        return new Ponto($x, $y);
    }
}
