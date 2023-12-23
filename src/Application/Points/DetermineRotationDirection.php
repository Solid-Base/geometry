<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Application\Points;

use Solidbase\Geometry\Domain\Factory\VectorFactory;
use Solidbase\Geometry\Domain\Point;

class DetermineRotationDirection
{
    public static function execute(Point $p1, Point $p2, Point $p3): RotationDirectionEnum
    {
        if (PointsAlignmentChecker::Check($p1, $p2, $p3)) {
            return RotationDirectionEnum::COLLINEAR;
        }
        $origem = $p1;
        $direcao = VectorFactory::CreateFromPoints($p1, $p3);
        $vetor = VectorFactory::CreateFromPoints($origem, $p2);
        $resultado = $direcao->crossProduct($vetor);
        if (sbLessThan($resultado->z, 0)) {
            return RotationDirectionEnum::CLOCKWISE;
        }

        return RotationDirectionEnum::COUNTERCLOCKWISE;
    }
}
