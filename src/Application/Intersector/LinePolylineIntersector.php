<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Application\Intersector;

use Solidbase\Geometry\Domain\Factory\LineFactory;
use Solidbase\Geometry\Domain\Factory\PolylineFactory;
use Solidbase\Geometry\Domain\Line;
use Solidbase\Geometry\Domain\Polyline;

class LinePolylineIntersector
{
    public static function GetPoints(Line $linha, Polyline $poligono): ?array
    {
        [$pontos,] = self::CalculateIntersection($linha, $poligono);
        if (0 == count($pontos)) {
            return null;
        }

        return $pontos;
    }

    public static function GetPolygon(Line $linha, Polyline $poligono): Polyline
    {
        [,$poligono] = self::CalculateIntersection($linha, $poligono);

        return $poligono;
    }

    private static function CalculateIntersection(Line $linhaIntersecao, Polyline $poligono): array
    {
        $polilinha = clone $poligono;
        $pontos = $polilinha->getPoints();
        $numeroPonto = \count($pontos);
        $pontosRetorno = [];
        $pontosPoligono = [];
        for ($i = 1; $i < $numeroPonto; ++$i) {
            $p1 = $pontos->get($i - 1);
            $p2 = $pontos->get($i);
            if (2 == count($pontosRetorno)) {
                $pontosPoligono[] = $p2;
                continue;
            }
            $linha = LineFactory::CreateFromPoints($p1, $p2);
            if ($linha->isParallel($linhaIntersecao)) {
                $pontosPoligono[] = $p2;

                continue;
            }
            $ponto = LineIntersector::Calculate($linhaIntersecao, $linha);
            if (false !== array_search($ponto, $pontosRetorno, false)) {
                $pontosPoligono[] = $p2;

                continue;
            }
            if ($linha->belongsToSegment($ponto)) {
                $pontosPoligono[] = $ponto;
                $pontosPoligono[] = $p2;
                $pontosRetorno[] = $ponto;

                continue;
            }

            $pontosPoligono[] = $p2;
        }
        $poligonoNovo = PolylineFactory::CreateFromPoints($pontosPoligono, fechado: $poligono->isPolygon());

        return [$pontosRetorno, $poligonoNovo];
    }
}
