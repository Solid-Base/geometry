<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Interseccao;

use Solidbase\Geometria\Dominio\Linha;
use Solidbase\Geometria\Dominio\Plano;
use Solidbase\Geometria\Dominio\Ponto;
use SolidBase\Matematica\Algebra\Decomposicao\LU;
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
        $decomposicao = LU::Decompor($matriz);
        if (eZero($decomposicao->Determinante())) {
            return null;
        }
        $matrizPonto = $decomposicao->ResolverSistema(new Matriz([[$d1->multiplicar(-1)], [$d2->multiplicar(-1)], [$d3->multiplicar(-1)]]))->obtenhaMatriz();
        $ponto = new Ponto($matrizPonto[0][0], $matrizPonto[1][0], $matrizPonto[2][0]);

        return new Linha($ponto, $direcaoReta, 1);
    }
}
