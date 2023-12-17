<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Application\Polygon;

use Solidbase\Geometry\Application\Points\DetermineRotationDirection;
use Solidbase\Geometry\Domain\Polyline;

class PolygonTypeChecker
{
    private function __construct() {}

    public static function Check(Polyline $poligono): PolygonTypeEnum
    {
        if (\count($poligono) < 3) {
            return PolygonTypeEnum::Concave;
        }
        $pontos = $poligono->getPoints();
        $quantidade = count($poligono);
        $orientacaoOriginal = null;
        for ($i = 2; $i < $quantidade; ++$i) {
            $p1 = $pontos->get($i - 2);
            $p2 = $pontos->get($i - 1);
            $p3 = $pontos->get($i);
            $orientacao = DetermineRotationDirection::execute($p1, $p2, $p3);
            $orientacaoOriginal ??= $orientacao;
            if ($orientacao !== $orientacaoOriginal) {
                return PolygonTypeEnum::Concave;
            }
        }

        return PolygonTypeEnum::Convex;
    }
}
