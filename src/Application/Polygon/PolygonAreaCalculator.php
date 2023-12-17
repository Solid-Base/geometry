<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Application\Polygon;

use Solidbase\Geometry\Domain\Polyline;

class PolygonAreaCalculator
{
    private function __construct() {}

    public static function Calculate(Polyline $poligono): ?float
    {
        $soma = 0;
        if (\count($poligono) <= 3) {
            return null;
        }
        $poligono->closePolyline();
        $pontos = $poligono->getPoints();
        foreach ($pontos as $i => $ponto) {
            if (!isset($pontos[$i + 1])) {
                break;
            }
            $proximo = $pontos[$i + 1];
            $soma += ($ponto->_x + $proximo->_x) * ($proximo->_y - $ponto->_y);
        }

        $area = $soma / 2;

        if (sbIsZero($area)) {
            return null;
        }

        return $area;
    }
}
