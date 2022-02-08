<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Offset;

use Solidbase\Geometria\Aplicacao\Offset\Enum\DirecaoOffsetPoligono;
use Solidbase\Geometria\Dominio\Circulo;

class OffsetCirculo
{
    private function __construct()
    {
    }

    public static function executar(float $offset, Circulo $circulo, DirecaoOffsetPoligono $direcao): Circulo
    {
        $raio = DirecaoOffsetPoligono::Interno == $direcao ? $circulo->raio - $offset : $circulo->raio + $offset;

        return new Circulo($circulo->centro, $raio);
    }
}
