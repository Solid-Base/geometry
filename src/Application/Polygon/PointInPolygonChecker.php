<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Application\Polygon;

use Solidbase\Geometry\Application\Intersector\LineIntersector;
use Solidbase\Geometry\Domain\Factory\LineFactory;
use Solidbase\Geometry\Domain\Factory\VectorFactory;
use Solidbase\Geometry\Domain\Line;
use Solidbase\Geometry\Domain\Polyline;
use Solidbase\Geometry\Domain\Point;

class PointInPolygonChecker
{
    private function __construct() {}

    public static function Check(Polyline $poligono, Point $ponto): bool
    {
        $poligono->closePolyline();
        $linhaComparacao = new Line($ponto, VectorFactory::CreateBaseX(), 1);
        $numeroPontos = \count($poligono);
        $contagem = 0;
        $pontos = $poligono->getPoints();
        for ($i = 0; $i < $numeroPontos - 1; ++$i) {
            $p1 = $pontos->get($i);
            $p2 = $pontos->get($i + 1);
            $linha = LineFactory::CreateFromPoints($p1, $p2);
            if ($linha->belongsToLine($ponto)) {
                continue;
            }
            if ($linha->isParallel($linhaComparacao)) {
                continue;
            }
            $pontoIntersecao = LineIntersector::Calculate($linha, $linhaComparacao);
            if (sbLessThan($pontoIntersecao->_x, $ponto->_x)) {
                continue;
            }
            if (sbIsZero($pontoIntersecao->distanceToPoint($p1))
            || sbIsZero($pontoIntersecao->distanceToPoint($p2))) {
                if ((sbIsZero(($pontoIntersecao->_y - $p1->_y))) && $pontoIntersecao->_y > ($p2->_y)) {
                    ++$contagem;
                }
                if ((sbIsZero(($pontoIntersecao->_y - $p2->_y))) && $pontoIntersecao->_y > ($p1->_y)) {
                    ++$contagem;
                }

                continue;
            }
            if (!$linha->belongsToSegment($pontoIntersecao)) {
                continue;
            }
            ++$contagem;
        }

        return 1 === $contagem % 2;
    }
}
