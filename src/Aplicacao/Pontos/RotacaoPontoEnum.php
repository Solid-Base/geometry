<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Pontos;

enum RotacaoPontoEnum:int
{
    case ANTI_HORARIO = 1;

    case HORARIO = -1;

    case ALINHADO = 0;
}
