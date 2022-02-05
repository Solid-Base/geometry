<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Pontos;

use Solidbase\Geometria\Dominio\Fabrica\VetorFabrica;
use Solidbase\Geometria\Dominio\Ponto;

class PontosAlinhados
{
    public static function executar(Ponto $p1, Ponto $p2, Ponto $p3): bool
    {
        $v1 = (VetorFabrica::apartirPonto($p1))->somar(VetorFabrica::BaseZ());
        $v2 = (VetorFabrica::apartirPonto($p2))->somar(VetorFabrica::BaseZ());
        $v3 = (VetorFabrica::apartirPonto($p3))->somar(VetorFabrica::BaseZ());
        $retorno = $v1->produtoMisto($v2, $v3);

        return eZero($retorno);
    }
}
