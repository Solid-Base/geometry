<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Application\Offset\Enum;

enum DirecaoOffsetPoligono: int
{
    case Interno = 1;

    case Externo = -1;
}
