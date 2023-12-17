<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Application\Polygon;

use Solidbase\Geometry\Domain\Factory\PolylineFactory;
use Solidbase\Geometry\Domain\Polyline;

class EquivalentRectangleCreator
{
    public function __construct() {}

    public function fromInertia(float|int $inerciaX, float|int $inerciaY): Polyline
    {
        $hx = ($inerciaX ** 3) * (12 ** 2) / $inerciaY;
        $h = $hx ** (1 / 8);
        $b = $inerciaX * 12 / ($h ** 3);

        return PolylineFactory::CreateFromLenghtAndWidht($b, $h);
    }

    public function fromArea(float|int $area): Polyline
    {
        $a = sqrt($area);

        return PolylineFactory::CreateFromLenghtAndWidht($a, $a);
    }
}
