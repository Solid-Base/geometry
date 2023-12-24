<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Application\Points;

enum RotationDirectionEnum: int
{
    case Counterclockwise = 1;

    case Clockwise = -1;

    case Collinear = 0;
}
