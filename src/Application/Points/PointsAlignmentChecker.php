<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Application\Points;

use Solidbase\Geometry\Domain\Factory\VectorFactory;
use Solidbase\Geometry\Domain\Point;

class PointsAlignmentChecker
{
    public static function Check(Point $p1, Point $p2, Point $p3): bool
    {
        $v1 = (VectorFactory::FromPoint($p1))->add(VectorFactory::CreateBaseZ());
        $v2 = (VectorFactory::FromPoint($p2))->add(VectorFactory::CreateBaseZ());
        $v3 = (VectorFactory::FromPoint($p3))->add(VectorFactory::CreateBaseZ());
        $retorno = $v1->tripleProduct($v2, $v3);

        return sbIsZero($retorno);
    }
}
