<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Application\Intersector;

use Solidbase\Geometry\Domain\Arc;
use Solidbase\Geometry\Domain\Circle;
use Solidbase\Geometry\Domain\Line;

class LineArcIntersector
{
    private function __construct() {}

    public static function executar(Line $linha, Arc $arco): ?array
    {
        $circulo = new Circle($arco->center, $arco->radius);

        return LineCircleIntersector::Calculate($linha, $circulo);
    }
}
