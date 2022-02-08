<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Interseccao;

use Solidbase\Geometria\Dominio\Arco;
use Solidbase\Geometria\Dominio\Circulo;
use Solidbase\Geometria\Dominio\Linha;

class InterseccaoLinhaArco
{
    private function __construct()
    {
    }

    public static function executar(Linha $linha, Arco $arco): ?array
    {
        $circulo = new Circulo($arco->centro, $arco->raio);

        return InterseccaoLinhaCirculo::executar($linha, $circulo);
    }
}
