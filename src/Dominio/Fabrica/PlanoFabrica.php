<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Dominio\Fabrica;

use Solidbase\Geometria\Dominio\Plano;
use Solidbase\Geometria\Dominio\Ponto;
use Solidbase\Geometria\Dominio\Vetor;

class PlanoFabrica
{
    public static function CriarPlanoNormalOrigem(Ponto $origem, Vetor $normal): Plano
    {
        return new Plano($origem, $normal);
    }

    public static function CriarPlanoOrigemBases(Ponto $origem, Vetor $baseX, Vetor $baseY): Plano
    {
        return new Plano($origem, $baseX->produtoVetorial($baseY));
    }

    public static function CriarPlanoTresPontos(Ponto $ponto1, Ponto $ponto2, Ponto $ponto3): Plano
    {
        $vetor1 = VetorFabrica::apartirDoisPonto($ponto1, $ponto2);
        $vetor2 = VetorFabrica::apartirDoisPonto($ponto1, $ponto3);

        return self::CriarPlanoOrigemBases($ponto1, $vetor1, $vetor2);
    }
}