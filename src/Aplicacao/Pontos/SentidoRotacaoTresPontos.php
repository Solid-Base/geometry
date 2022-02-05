<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Pontos;

use Solidbase\Geometria\Dominio\Fabrica\VetorFabrica;
use Solidbase\Geometria\Dominio\Ponto;

class SentidoRotacaoTresPontos
{
    public static function executar(Ponto $p1, Ponto $p2, Ponto $p3): RotacaoPontoEnum
    {
        if (PontosAlinhados::executar($p1, $p2, $p3)) {
            return RotacaoPontoEnum::ALINHADO;
        }
        $origem = $p1;
        $direcao = VetorFabrica::apartirDoisPonto($p1, $p3);
        $vetor = VetorFabrica::apartirDoisPonto($origem, $p2);
        $resultado = $direcao->produtoVetorial($vetor);
        if (eMaior($resultado->z, 0)) {
            return RotacaoPontoEnum::HORARIO;
        }

        return RotacaoPontoEnum::ANTI_HORARIO;
    }
}
