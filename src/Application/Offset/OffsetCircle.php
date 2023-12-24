<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Application\Offset;

use Solidbase\Geometry\Application\Offset\Enum\DirecaoOffsetPoligono;
use Solidbase\Geometry\Domain\Circle;

class OffsetCircle
{
    private function __construct() {}

    public static function Generate(int|float $offset, Circle $circulo, DirecaoOffsetPoligono $direcao): Circle
    {
        $raio = DirecaoOffsetPoligono::Internal == $direcao ? ($circulo->radius - $offset) : ($circulo->radius + $offset);
        $raio = sbLessThan($raio, 0) ? 0 : $raio;

        return new Circle($circulo->center, $raio);
    }
}
