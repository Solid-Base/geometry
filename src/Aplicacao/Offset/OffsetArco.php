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

    public static function executar(float|int $offset, Arco $arco, DirecaoOffsetPoligono $direcao): Arco
    {
        $raio = DirecaoOffsetPoligono::Interno == $direcao ? $arco->raio - $offset : $arco->raio + $offset;
        $raio = eMenor($raio, 0) ? 0 : $raio;

        return new Arco($arco->centro, $raio, $arco->anguloInicial, $arco->anguloFinal);
    }
}
