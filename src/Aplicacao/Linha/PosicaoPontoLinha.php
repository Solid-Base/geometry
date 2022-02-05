<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Linha;

use Solidbase\Geometria\Dominio\Fabrica\VetorFabrica;
use Solidbase\Geometria\Dominio\Linha;
use Solidbase\Geometria\Dominio\Ponto;

class PosicaoPontoLinha
{
    public static function executar(Linha $linha, Ponto $ponto): PosicaoPontoEnum
    {
        if ($linha->pontoPertenceLinha($ponto)) {
            return PosicaoPontoEnum::SOBRE;
        }

        $origem = $linha->origem;
        $direcao = $linha->direcao;
        $vetor1 = VetorFabrica::apartirDoisPonto($origem, $ponto);
        $resultado = $direcao->produtoVetorial($vetor1);
        if (eMaior($resultado->z, 0)) {
            return PosicaoPontoEnum::ESQUERDA;
        }

        return PosicaoPontoEnum::DIREITA;
    }
}
