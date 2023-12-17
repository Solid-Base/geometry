<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Application\Polygon;

use Solidbase\Geometry\Application\Intersector\LineCircleIntersector;
use Solidbase\Geometry\Domain\Circle;
use Solidbase\Geometry\Domain\Factory\LineFactory;
use Solidbase\Geometry\Domain\Polyline;

class CirclePolygonIntersectionChecker
{
    public function __construct(private Polyline $poligono) {}

    public static function Check(Polyline $poligono, Circle $circulo): bool
    {
        $pontos = $poligono->getPoints();
        $quantidade = \count($pontos);
        for ($i = 1; $i < $quantidade; ++$i) {
            $p1 = $pontos->get($i - 1);
            $p2 = $pontos->get($i);
            $linha = LineFactory::CreateFromPoints($p1, $p2);
            if (!LineCircleIntersector::CheckLineCircleIntersection($linha, $circulo)) {
                continue;
            }
            $linhaIntersecao = LineCircleIntersector::Calculate($linha, $circulo);
            if (null === $linhaIntersecao) {
                return false;
            }
            $origem = $linhaIntersecao[0];
            $final = $linhaIntersecao[1] ?? $origem;
            if ($linha->belongsToSegment($origem) || $linha->belongsToSegment($final)) {
                return true;
            }
        }

        return false;
    }
}
