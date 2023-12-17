<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Application\Polygon;

use Solidbase\Geometry\Domain\Point;

class DataPolygon
{
    public function __construct(
        public readonly float $area,
        public readonly int $sense,
        public readonly PolygonTypeEnum $type,
        public readonly Point $center,
        public readonly float $segundoMomentoInerciaX,
        public readonly float $segundoMomentoInerciaY,
        public readonly float $momentoInerciaPrincipalX,
        public readonly float $momentoInerciaPrincipalY,
    ) {}
}
