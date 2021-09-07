<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Dominio\Fabrica;

use Solidbase\Geometria\Dominio\Linha;
use Solidbase\Geometria\Dominio\Ponto;

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
}
