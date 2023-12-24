<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Application\Offset;

use Solidbase\Geometry\Application\Offset\Enum\DirecaoOffsetPoligono;
use Solidbase\Geometry\Domain\Arc;

class OffsetArc
{
    private function __construct() {}

    public static function Generate(float|int $offset, Arc $arc, DirecaoOffsetPoligono $direction): Arc
    {
        $raio = DirecaoOffsetPoligono::Internal == $direction ? $arc->radius - $offset : $arc->radius + $offset;
        $raio = sbLessThan($raio, 0) ? 0 : $raio;

        return new Arc($arc->center, $raio, $arc->startAngle, $arc->endAngle);
    }
}
