<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Application\Offset\Enum;

enum DirecaoOffsetPoligono: int
{
    case Internal = 1;

    case External = -1;
}
