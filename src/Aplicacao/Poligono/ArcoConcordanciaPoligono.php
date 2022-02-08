<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Poligono;

use Solidbase\Geometria\Aplicacao\Interseccao\InterseccaoLinhas;
use Solidbase\Geometria\Aplicacao\Modificadores\Transformacao;
use Solidbase\Geometria\Dominio\Arco;
use Solidbase\Geometria\Dominio\Fabrica\ArcoCirculoFabrica;
use Solidbase\Geometria\Dominio\Fabrica\LinhaFabrica;
use Solidbase\Geometria\Dominio\Fabrica\VetorFabrica;
use Solidbase\Geometria\Dominio\PontoPoligono;

class ArcoConcordanciaPoligono
{
    public static function executar(PontoPoligono $p1, PontoPoligono $p2): Arco
    {
        $angulo = atan($p1->concordancia) * 4;
        $rotacao = subtrair((numero(S_PI))->dividir(2), dividir($angulo, 2))->valor();
        $transformacao = Transformacao::criarRotacaoPonto(VetorFabrica::BaseZ(), $rotacao, $p1);
        $p2Novo = $transformacao->dePonto($p2);
        $transformacao = Transformacao::criarRotacaoPonto(VetorFabrica::BaseZ(), -$rotacao, $p2);
        $p1Novo = $transformacao->dePonto($p1);
        $linha1 = LinhaFabrica::apartirDoisPonto($p1, $p2Novo);
        $linha2 = LinhaFabrica::apartirDoisPonto($p2, $p1Novo);
        $centro = InterseccaoLinhas::executar($linha1, $linha2);
        $raio = arredondar($p1->distanciaParaPonto($centro), bcscale() - 2);
        $direcao = VetorFabrica::apartirDoisPonto($centro, $p1->pontoMedio($p2))->vetorUnitario();
        $p3 = $centro->somar($direcao->escalar($raio));

        return ArcoCirculoFabrica::arcoTresPontos($p1, $p3, $p2);
    }
}
