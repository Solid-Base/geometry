<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Application\Polygon;

use Solidbase\Geometry\Application\Intersector\LineIntersector;
use Solidbase\Geometry\Application\Transform\Transform;
use Solidbase\Geometry\Domain\Factory\LineFactory;
use Solidbase\Geometry\Domain\Factory\VectorFactory;
use Solidbase\Geometry\Domain\PontoPoligono;

class PontoConcordanciaPoligono
{
    public static function executar(PontoPoligono $p1, PontoPoligono $p2): array
    {
        $angulo = atan($p1->agreement) * 4;
        $rotacao = ($angulo / 2);
        $transformacao = Transform::CreateRotationAroundPoint(VectorFactory::CreateBaseZ(), -$rotacao, $p1);
        $p2Novo = $transformacao->applyToPoint($p2);
        $transformacao = Transform::CreateRotationAroundPoint(VectorFactory::CreateBaseZ(), $rotacao, $p2);
        $p1Novo = $transformacao->applyToPoint($p1);

        $linha1 = LineFactory::CreateFromPoints($p1, $p2Novo);
        $linha2 = LineFactory::CreateFromPoints($p2, $p1Novo);
        $pontoIntersecao = LineIntersector::Calculate($linha1, $linha2);
        $distancia = $p1->distanceToPoint($pontoIntersecao);
        $raio = abs($distancia / tan($rotacao));

        return [$pontoIntersecao, $raio];
    }
}
