<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Offset\Enum;

enum DirecaoOffsetReta:int
{
    case Direita = 1;

    case Esquerda = -1;
}
