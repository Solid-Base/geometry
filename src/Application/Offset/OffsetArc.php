<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Application\Offset;

use Solidbase\Geometry\Application\Offset\Enum\DirecaoOffsetPoligono;
use Solidbase\Geometry\Domain\Arc;

class OffsetArc
{
    private function __construct() {}

    public static function Generate(float|int $offset, Arc $arco, DirecaoOffsetPoligono $direcao): Arc
    {
        $raio = DirecaoOffsetPoligono::Interno == $direcao ? $arco->radius - $offset : $arco->radius + $offset;
        $raio = sbLessThan($raio, 0) ? 0 : $raio;

        return new Arc($arco->center, $raio, $arco->startAngle, $arco->endAngle);
    }
}
