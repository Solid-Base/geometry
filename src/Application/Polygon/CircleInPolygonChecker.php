<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Application\Polygon;

use Solidbase\Geometry\Domain\Circle;
use Solidbase\Geometry\Domain\Factory\LineFactory;
use Solidbase\Geometry\Domain\Polyline;

class CircleInPolygonChecker
{
    private function __construct() {}

    public static function Check(Polyline $poligono, Circle $circulo): bool
    {
        $centro = $circulo->center;
        if (!PointInPolygonChecker::Check($poligono, $circulo->center)) {
            return false;
        }
        $pontos = $poligono->getPoints();
        $quantidade = \count($pontos);
        for ($i = 1; $i < $quantidade; ++$i) {
            $p1 = $pontos->get($i - 1);
            $p2 = $pontos->get($i);
            $linha = LineFactory::CreateFromPoints($p1, $p2);
            if ($linha->distanceFromPoint($centro) < ($circulo->radius)) {
                return false;
            }
        }

        return true;
    }
}
