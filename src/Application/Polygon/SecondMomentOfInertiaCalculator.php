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
            $comum = ($ponto->_x * $proximo->_y) - ($proximo->_x * $ponto->_y);
            $somaY += ($proximo->_x ** 2 + ($ponto->_x * $proximo->_x) + $ponto->_x ** 2) * $comum;
            $somaX += (($proximo->_y ** 2) + ($ponto->_y * $proximo->_y) + $ponto->_y ** 2) * $comum;
        }

        $x = ($somaX / 12);
        $y = ($somaY / 12);

        return [$x, $y];
    }
}
