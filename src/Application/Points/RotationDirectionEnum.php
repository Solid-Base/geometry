<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Application\Points;

enum RotationDirectionEnum: int
{
    case COUNTERCLOCKWISE = 1;

    case CLOCKWISE = -1;

    case COLLINEAR = 0;
}
