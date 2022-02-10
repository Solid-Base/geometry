<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Offset;

use Solidbase\Geometria\Aplicacao\Offset\Enum\DirecaoOffsetPoligono;
use Solidbase\Geometria\Dominio\Arco;
use SolidBase\Matematica\Aritimetica\Numero;

class OffsetArco
{
    private function __construct()
    {
    }

    public static function executar(float|Numero $offset, Arco $arco, DirecaoOffsetPoligono $direcao): Arco
    {
        $raio = DirecaoOffsetPoligono::Interno == $direcao ? subtrair($arco->raio, $offset) : somar($arco->raio, $offset);
        $raio = eMenor($raio, 0) ? numero(0) : $raio;

        return new Arco($arco->centro, $raio, $arco->anguloInicial, $arco->anguloFinal);
    }
}
