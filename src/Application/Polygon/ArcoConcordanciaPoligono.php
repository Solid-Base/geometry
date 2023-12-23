<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Application\Polygon;

use Solidbase\Geometry\Application\Intersector\LineIntersector;
use Solidbase\Geometry\Application\Transform\Transform;
use Solidbase\Geometry\Domain\Arc;
use Solidbase\Geometry\Domain\Factory\CircleArchFactory;
use Solidbase\Geometry\Domain\Factory\LineFactory;
use Solidbase\Geometry\Domain\Factory\VectorFactory;
use Solidbase\Geometry\Domain\PointOfPolygon;

class ArcoConcordanciaPoligono
{
    public static function executar(PointOfPolygon $p1, PointOfPolygon $p2): Arc
    {
        $angulo = atan($p1->agreement) * 4;
        $rotacao = (M_PI / 2 - $angulo / 2);
        $transformacao = Transform::CreateRotationAroundPoint(VectorFactory::CreateBaseZ(), $rotacao, $p1);
        $p2Novo = $transformacao->applyToPoint($p2);
        $transformacao = Transform::CreateRotationAroundPoint(VectorFactory::CreateBaseZ(), -$rotacao, $p2);
        $p1Novo = $transformacao->applyToPoint($p1);
        $linha1 = LineFactory::CreateFromPoints($p1, $p2Novo);
        $linha2 = LineFactory::CreateFromPoints($p2, $p1Novo);
        $centro = LineIntersector::Calculate($linha1, $linha2);
        $raio = sbRound($p1->distanceToPoint($centro), ACCURACY_SOLIDBASE);
        $direcao = VectorFactory::CreateFromPoints($centro, $p1->midpoint($p2))->getUnitary();
        $p3 = $centro->add($direcao->scalar($raio));

        return CircleArchFactory::CreateArcFromThreePoint($p1, $p3, $p2);
    }
}
