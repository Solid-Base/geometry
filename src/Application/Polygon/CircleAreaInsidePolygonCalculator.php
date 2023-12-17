<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Application\Polygon;

use Solidbase\Geometry\Application\Intersector\LineCircleIntersector;
use Solidbase\Geometry\Domain\Circle;
use Solidbase\Geometry\Domain\Factory\CircleArchFactory;
use Solidbase\Geometry\Domain\Factory\LineFactory;
use Solidbase\Geometry\Domain\Factory\PolylineFactory;
use Solidbase\Geometry\Domain\Factory\VectorFactory;
use Solidbase\Geometry\Domain\Line;
use Solidbase\Geometry\Domain\Polyline;
use Solidbase\Geometry\Domain\Point;

class CircleAreaInsidePolygonCalculator
{
    public static function Calculate(Polyline $polilinha, Circle $circulo): float
    {
        $polilinha->closePolyline();
        $pontos = $polilinha->getPoints();
        $quantidade = count($pontos);
        $pontosIntersecao = [];
        for ($i = 1; $i < $quantidade; ++$i) {
            $p1 = $pontos->get($i - 1);
            $p2 = $pontos->get($i);
            $linha = LineFactory::CreateFromPoints($p2, $p1);
            if (!LineCircleIntersector::CheckLineCircleIntersection($linha, $circulo)) {
                continue;
            }
            $linhaIntersecao = LineCircleIntersector::Calculate($linha, $circulo);
            $origem = $linhaIntersecao[0];
            $final = $linhaIntersecao[1] ?? $origem;
            if ($circulo->pointInnerCircle($p1) || $circulo->pointBelongsToBorderCircle($p1)) {
                $pontosIntersecao[] = $p1;
            }
            if ($linha->belongsToSegment($origem)) {
                $pontosIntersecao[] = $origem;
            }
            if ($linha->belongsToSegment($final)) {
                $pontosIntersecao[] = $final;
            }
            if ($circulo->pointInnerCircle($p2) || $circulo->pointBelongsToBorderCircle($p2)) {
                $pontosIntersecao[] = $p2;
            }
        }
        if (0 == count($pontosIntersecao)) {
            return 0;
        }
        $primeiro = self::primeiroCruzarCirculo($pontosIntersecao, $circulo);
        $ultimo = self::ultimoCruzarCirculo($pontosIntersecao, $circulo);
        $direcao = (VectorFactory::CreateFromPoints($primeiro, $ultimo))->crossProduct(VectorFactory::CreateBaseZ());
        if ($direcao->isZero()) {
            return 0;
        }
        $linha = new Line($primeiro->midpoint($ultimo), $direcao, 1);
        $linhaIntersecao = LineCircleIntersector::Calculate($linha, $circulo);
        $origem = $linhaIntersecao[0];
        $final = $linhaIntersecao[1] ?? $origem;
        $pontoArco = PointInPolygonChecker::Check($polilinha, $origem) ? $origem : $final;
        $arco = CircleArchFactory::CreateArcFromThreePoint($primeiro, $pontoArco, $ultimo);
        $areaArco = $arco->area();
        $poligonoNovo = PolylineFactory::CreateFromPoints($pontosIntersecao, true, fechado: true);
        if (count($poligonoNovo) <= 3) {
            return $areaArco;
        }
        $propriedade = PolygonPropertiesCalculator::Calculate($poligonoNovo);

        return $areaArco + $propriedade->area;
    }

    public static function primeiroCruzarCirculo(array $pontos, Circle $circulo): Point
    {
        foreach ($pontos as $ponto) {
            if ($circulo->pointBelongsToBorderCircle($ponto)) {
                return $ponto;
            }
        }
    }

    public static function ultimoCruzarCirculo(array $pontos, Circle $circulo): Point
    {
        $pontos = array_reverse($pontos);
        foreach ($pontos as $ponto) {
            if ($circulo->pointBelongsToBorderCircle($ponto)) {
                return $ponto;
            }
        }
    }
}
