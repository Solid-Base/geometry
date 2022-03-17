<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Interseccao;

use Solidbase\Geometria\Dominio\Linha;
use Solidbase\Geometria\Dominio\Plano;
use Solidbase\Geometria\Dominio\Ponto;
use SolidBase\Matematica\Algebra\Decomposicao\DecomposicaoLU;
use SolidBase\Matematica\Algebra\Matriz;

class InterseccaoPlanos
{
    public static function executar(Plano $plano1, Plano $plano2): ?Linha
    {
        if ($plano1->normal->temMesmoSentido($plano2->normal)) {
            return null;
        }
        $direcaoReta = $plano1->normal->produtoVetorial($plano2->normal);
        $plano3 = new Plano($plano1->origem, $direcaoReta);
        [$x1, $y1, $z1, $d1] = $plano1->equacaoPlano();
        [$x2, $y2, $z2, $d2] = $plano2->equacaoPlano();
        [$x3, $y3, $z3, $d3] = $plano3->equacaoPlano();
        $matriz = new Matriz([[$x1, $y1, $z1], [$x2, $y2, $z2], [$x3, $y3, $z3]]);
        $decomposicao = DecomposicaoLU::Decompor($matriz);
        if (eZero($decomposicao->Determinante())) {
            return null;
        }
        $matrizPonto = $decomposicao->ResolverSistema(new Matriz([[-$d1], [-$d2], [-$d3]]));
        $ponto = new Ponto($matrizPonto['1'], $matrizPonto['2'], $matrizPonto['3']);

        return new Linha($ponto, $direcaoReta, 1);
    }
}
