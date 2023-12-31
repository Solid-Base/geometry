<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Application\Polygon;

use Solidbase\Geometry\Domain\Polyline;

class SecondMomentOfInertiaCalculator
{
    private function __construct() {}

    /**
     * @return ?float[]
     */
    public static function Calculate(Polyline $poligono): ?array
    {
        if (\count($poligono) < 3) {
            return null;
        }
        $somaX = 0;
        $somaY = 0;
        $pontos = $poligono->getPoints();
        $numPontos = \count($poligono);

        for ($i = 0; $i < $numPontos - 1; ++$i) {
            $ponto = $pontos[$i];
            $proximo = $pontos[$i + 1];
            $comum = ($ponto->x * $proximo->y) - ($proximo->x * $ponto->y);
            $somaY += ($proximo->x ** 2 + ($ponto->x * $proximo->x) + $ponto->x ** 2) * $comum;
            $somaX += (($proximo->y ** 2) + ($ponto->y * $proximo->y) + $ponto->y ** 2) * $comum;
        }

        $x = ($somaX / 12);
        $y = ($somaY / 12);

        return [$x, $y];
    }
}
