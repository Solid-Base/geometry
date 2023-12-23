<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Domain\Factory;

use Solidbase\Geometry\Domain\Plane;
use Solidbase\Geometry\Domain\Point;
use Solidbase\Geometry\Domain\Vector;

class PlaneFactory
{
    public static function CreateFromNormalAndOrigin(Point $origem, Vector $normal): Plane
    {
        return new Plane($origem, $normal);
    }

    public static function CreateFromOriginBases(Point $origem, Vector $baseX, Vector $baseY): Plane
    {
        return new Plane($origem, $baseX->crossProduct($baseY));
    }

    public static function CreateFromThreePoints(Point $ponto1, Point $ponto2, Point $ponto3): Plane
    {
        $vetor1 = VectorFactory::CreateFromPoints($ponto1, $ponto2);
        $vetor2 = VectorFactory::CreateFromPoints($ponto1, $ponto3);

        return self::CreateFromOriginBases($ponto1, $vetor1, $vetor2);
    }

    public static function CreatePlanX(): Plane
    {
        return self::CreateFromOriginBases(new Point(), VectorFactory::CreateBaseY(), VectorFactory::CreateBaseZ());
    }

    public static function CreatePlanY(): Plane
    {
        return self::CreateFromOriginBases(new Point(), VectorFactory::CreateBaseX(), VectorFactory::CreateBaseZ());
    }

    public static function CreatePlanZ(): Plane
    {
        return self::CreateFromOriginBases(new Point(), VectorFactory::CreateBaseX(), VectorFactory::CreateBaseY());
    }
}
