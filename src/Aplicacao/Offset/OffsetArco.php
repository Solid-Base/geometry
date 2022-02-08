<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Offset;

use Solidbase\Geometria\Aplicacao\Offset\Enum\DirecaoOffsetPoligono;
use Solidbase\Geometria\Dominio\Arco;

class OffsetArco
{
    private function __construct()
    {
    }

    public static function executar(float $offset, Arco $arco, DirecaoOffsetPoligono $direcao): Arco
    {
        $raio = max(DirecaoOffsetPoligono::Interno == $direcao ? $arco->raio - $offset : $arco->raio + $offset, 0);

        return new Arco($arco->centro, $raio, $arco->anguloInicial, $arco->anguloFinal);
    }
}
