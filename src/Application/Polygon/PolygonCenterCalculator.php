<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Application\Polygon;

use Solidbase\Geometry\Domain\Polyline;
use Solidbase\Geometry\Domain\Point;

class PolygonCenterCalculator
{
    private function __construct() {}

    public static function Calculate(Polyline $poligono): ?Point
    {
        $area = PolygonAreaCalculator::Calculate($poligono);
        if (null === $area) {
            return null;
        }
        $somaX = 0;
        $somaY = 0;
        $pontos = $poligono->getPoints();
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

        return new Point($x, $y);
    }
}
