<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Offset;

use Solidbase\Geometria\Aplicacao\Offset\Enum\DirecaoOffsetPoligono;
use Solidbase\Geometria\Dominio\Circulo;
use SolidBase\Matematica\Aritimetica\Numero;

class OffsetCirculo
{
    private function __construct()
    {
    }

    public static function executar(Numero|float $offset, Circulo $circulo, DirecaoOffsetPoligono $direcao): Circulo
    {
        $raio = DirecaoOffsetPoligono::Interno == $direcao ? subtrair($circulo->raio, $offset) : somar($circulo->raio, $offset);
        $raio = eMenor($raio, 0) ? numero(0) : $raio;

        return new Circulo($circulo->centro, $raio);
    }
}
