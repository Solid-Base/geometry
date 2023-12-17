<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Application\Polygon;

use Solidbase\Geometry\Domain\Polyline;

class PolygonPerimeterCalculator
{
    private function __construct() {}

    public static function Calculate(Polyline $poligono): float
    {
        $soma = 0;
        if (\count($poligono) < 2) {
            return 0;
        }
        $pontos = $poligono->getPoints();
        $quantidade = count($pontos);
        for ($i = 1; $i < $quantidade; ++$i) {
            $p1 = $pontos->get($i - 1);
            $p2 = $pontos->get($i);
            $soma += ($p1->distanceToPoint($p2));
        }
        if ($poligono->isPolygon()) {
            $ultimo = $pontos->last();
            $soma += $pontos->first()->distanceToPoint($ultimo);
        }

        return $soma;
    }
}
