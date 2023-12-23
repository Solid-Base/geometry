<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Domain\Factory;

use DomainException;
use Solidbase\Geometry\Application\Intersector\LineIntersector;
use Solidbase\Geometry\Application\Points\PointsAlignmentChecker;
use Solidbase\Geometry\Application\Points\RotationDirectionEnum;
use Solidbase\Geometry\Application\Points\DetermineRotationDirection;
use Solidbase\Geometry\Domain\Arc;
use Solidbase\Geometry\Domain\Circle;
use Solidbase\Geometry\Domain\Line;
use Solidbase\Geometry\Domain\Point;

class CircleArchFactory
{
    public static function CreateArcFromThreePoint(Point $point, Point $point2, Point $point3): Arc
    {
        $center = self::GetCenterThreePoints($point, $point2, $point3);
        $radius = $center->distanceToPoint($point);

        $vetorP1 = VectorFactory::CreateFromPoints($center, $point);
        $anguloP1 = $vetorP1->getAbsoluteAngle();
        $vetorP3 = VectorFactory::CreateFromPoints($center, $point3);
        $anguloP3 = $vetorP3->getAbsoluteAngle();

        $rotacao = DetermineRotationDirection::execute($point, $point2, $point3);

        $anguloInicial = RotationDirectionEnum::COUNTERCLOCKWISE == $rotacao ? $anguloP1 : $anguloP3;
        $anguloFinal = RotationDirectionEnum::COUNTERCLOCKWISE == $rotacao ? $anguloP3 : $anguloP1;

        return new Arc($center, $radius, $anguloInicial, $anguloFinal);
    }

    public static function CreateArcFromCenterStartEnd(Point $center, Point $start, Point $end): Arc
    {
        $vetorP1 = VectorFactory::CreateFromPoints($center, $start);
        $anguloP1 = $vetorP1->getAbsoluteAngle();
        $vetorP2 = VectorFactory::CreateFromPoints($center, $end);
        $anguloP2 = $vetorP2->getAbsoluteAngle();
        $raio = $center->distanceToPoint($start);

        return new Arc($center, $raio, $anguloP1, $anguloP2);
    }

    public static function CreateCircleFromThreePoints(Point $ponto1, Point $ponto2, Point $ponto3): Circle
    {
        $centro = self::GetCenterThreePoints($ponto1, $ponto2, $ponto3);
        $raio = $centro->distanceToPoint($ponto1);

        return new Circle($centro, $raio);
    }

    private static function GetIntersectionLine(Line $linha1, Line $linha2): Point
    {
        return LineIntersector::Calculate($linha1, $linha2);
    }

    private static function GetCenterThreePoints(Point $ponto1, Point $ponto2, Point $ponto3): Point
    {
        if (PointsAlignmentChecker::Check($ponto1, $ponto2, $ponto3)) {
            throw new DomainException('Para fazer um arco ou circulo apartir de três pontos, é necessario que os mesmos não seja alinhado.');
        }
        $v1 = VectorFactory::CreateFromPoints($ponto1, $ponto2)->crossProduct(VectorFactory::CreateBaseZ());
        $v2 = VectorFactory::CreateFromPoints($ponto1, $ponto3)->crossProduct(VectorFactory::CreateBaseZ());
        $pontoMedioP1P2 = $ponto1->midpoint($ponto2);
        $pontoMedioP1P3 = $ponto1->midpoint($ponto3);
        $retaP1P2 = new Line($pontoMedioP1P2, $v1, 1);
        $retaP1P3 = new Line($pontoMedioP1P3, $v2, 1);

        return self::GetIntersectionLine($retaP1P2, $retaP1P3);
    }
}
