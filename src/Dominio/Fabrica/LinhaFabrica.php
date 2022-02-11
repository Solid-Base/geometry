<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Dominio\Fabrica;

use Solidbase\Geometria\Dominio\Linha;
use Solidbase\Geometria\Dominio\Ponto;
use Solidbase\Geometria\Dominio\Vetor;

class LinhaFabrica
{
    public static function apartirDoisPonto(Ponto $ponto1, Ponto $ponto2): Linha
    {
        $vetor = VetorFabrica::apartirDoisPonto($ponto1, $ponto2);
        if ($vetor->eNulo()) {
            $vetor = VetorFabrica::BaseX();
        }
        $comprimento = $ponto2->distanciaParaPonto($ponto1);

        return new Linha($ponto1, $vetor->vetorUnitario(), $comprimento);
    }

    public static function origemDirecao(Ponto $origem, Vetor $direcao): Linha
    {
        return new Linha($origem, $direcao, 1);
    }

    public static function equacaoReta(?float $coeficienteAngular, float $coeficienteLinear): Linha
    {
        if (null === $coeficienteAngular) {
            return new Linha(new Ponto($coeficienteLinear, 0), VetorFabrica::BaseY(), 1);
        }
        if (eZero($coeficienteAngular)) {
            return new Linha(new Ponto(0, $coeficienteLinear), VetorFabrica::BaseX(), 1);
        }
        $p1 = new Ponto(0, $coeficienteLinear);
        $p2 = new Ponto(1, ($coeficienteAngular + $coeficienteLinear));

        return self::apartirDoisPonto($p1, $p2);
    }
}
