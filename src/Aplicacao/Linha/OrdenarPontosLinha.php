<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Linha;

use Solidbase\Geometria\Aplicacao\Interseccao\InterseccaoLinhas;
use Solidbase\Geometria\Dominio\Fabrica\VetorFabrica;
use Solidbase\Geometria\Dominio\Linha;
use Solidbase\Geometria\Dominio\Ponto;

class OrdenarPontosLinha
{
    /**
     * Undocumented function.
     *
     * @param Ponto[] $pontos
     *
     * @return Ponto[]
     */
    public static function executar(Linha $linha, array $pontos): array
    {
        $direcao = $linha->direcao;
        $direcaoPerpendicular = VetorFabrica::Perpendicular($linha->direcao);
        $pontosLinha = [];
        $origem = $linha->origem;
        foreach ($pontos as $key => $ponto) {
            $linha1 = new Linha($ponto, $direcaoPerpendicular, 1);
            $pontoIntersecao = InterseccaoLinhas::executar($linha, $linha1);
            $direcaoTeste = VetorFabrica::apartirDoisPonto($origem, $pontoIntersecao);
            if (!$direcaoTeste->temMesmoSentido($direcao)) {
                $origem = $pontoIntersecao;
            }
            $pontosLinha[$key] = $pontoIntersecao;
        }
        $retorno = [];
        $distancias = array_map(fn (Ponto $p) => $p->distanciaParaPonto($origem)->valor(), $pontosLinha);
        asort($distancias);
        foreach (array_keys($distancias) as $key) {
            $retorno[] = $pontos[$key];
        }

        return $retorno;
    }
}
